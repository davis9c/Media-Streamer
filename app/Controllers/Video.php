<?php

namespace App\Controllers;

use App\Libraries\HlsConverter;
use App\Models\VideoModel;

class Video extends BaseController
{
    protected $videoModel;
    protected HlsConverter $hlsConverter;

    public function __construct()
    {
        $this->videoModel   = new VideoModel();
        $this->hlsConverter = new HlsConverter();
    }

    // =========================
    // WEB VIEW
    // =========================
    public function index()
    {
        $videos = $this->videoModel->orderBy('id', 'DESC')->findAll();

        return view('video', ['videos' => $videos]);
    }

    public function upload()
    {
        $video = $this->request->getFile('video');
        $title = trim((string) $this->request->getPost('title'));

        if ($title === '') {
            return redirect()->back()->with('error', 'Judul video wajib diisi');
        }

        if (! $video || ! $video->isValid()) {
            return redirect()->back()
                ->with('error', $video ? $video->getErrorString() : 'File tidak terkirim');
        }

        $allowed = [
            'video/mp4',
            'video/x-m4v',
            'video/quicktime',
        ];

        if (! in_array($video->getMimeType(), $allowed, true)) {
            return redirect()->back()->with('error', 'Format video tidak didukung');
        }

        // Batas 2 GB (sesuaikan juga upload_max_filesize di php.ini).
        if ($video->getSize() > 2 * 1024 * 1024 * 1024) {
            return redirect()->back()->with('error', 'Ukuran video maksimal 2 GB');
        }

        $uploadDir = WRITEPATH . 'uploads';
        $this->ensureDirectory($uploadDir);

        $newName = $video->getRandomName();

        if (! $video->move($uploadDir, $newName)) {
            return redirect()->back()->with('error', 'Gagal menyimpan file video');
        }

        $videoId = $this->videoModel->insert([
            'title'    => $title,
            'filename' => $newName,
            'hls_path' => null,
        ], true);

        if (! $videoId) {
            @unlink($uploadDir . DIRECTORY_SEPARATOR . $newName);

            return redirect()->back()->with('error', 'Gagal menyimpan data video');
        }

        $this->ensureDirectory($this->hlsConverter->storageDirectory());

        $hlsPath = $this->hlsConverter->convert((int) $videoId, $uploadDir . DIRECTORY_SEPARATOR . $newName);

        if ($hlsPath !== null) {
            $this->videoModel->update($videoId, ['hls_path' => $hlsPath]);

            return redirect()->to(base_url('video'))
                ->with('success', 'Video berhasil diupload dan dikonversi ke HLS');
        }

        log_message('warning', 'HLS tidak dibuat untuk video {id}; fallback MP4 digunakan.', ['id' => $videoId]);

        return redirect()->to(base_url('video'))
            ->with('success', 'Video berhasil diupload (HLS tidak tersedia, memakai MP4)');
    }

    // =========================
    // STREAM FILE MP4
    // =========================
    public function stream($filename)
    {
        $filename = basename((string) $filename);
        $path     = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $filename;

        if ($filename === '' || ! is_file($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'video/mp4')
            ->setHeader('Content-Length', (string) filesize($path))
            ->setBody(file_get_contents($path));
    }

    // =========================
    // STREAM HLS (m3u8 / .ts)
    // =========================
    public function hls($id, $file)
    {
        $id   = (int) $id;
        $file = basename((string) $file);

        if ($id < 1 || ! preg_match('/^[A-Za-z0-9_.-]+$/', $file)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mimeTypes = [
            'm3u8' => 'application/vnd.apple.mpegurl',
            'ts'   => 'video/mp2t',
        ];

        if (! isset($mimeTypes[$extension])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $baseDir = $this->hlsConverter->storageDirectory() . DIRECTORY_SEPARATOR . $id;
        $path    = $baseDir . DIRECTORY_SEPARATOR . $file;

        $realBase = realpath($baseDir);
        $realPath = realpath($path);

        if ($realBase === false || $realPath === false
            || ($realPath !== $realBase && ! str_starts_with($realPath, $realBase . DIRECTORY_SEPARATOR))) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', $mimeTypes[$extension])
            ->setHeader('Content-Length', (string) filesize($realPath))
            ->setHeader('Cache-Control', $extension === 'm3u8' ? 'no-cache' : 'public, max-age=3600')
            ->setBody(file_get_contents($realPath));
    }

    // =========================
    // API SECTION
    // =========================

    /**
     * GET /api/videos
     * Ambil semua video
     */
    public function apiList()
    {
        $videos = array_map([$this, 'withHlsUrl'], $this->videoModel->findAll());

        return $this->response->setJSON([
            'status' => true,
            'data'   => $videos,
        ]);
    }

    /**
     * GET /api/videos/{id}
     * Ambil detail 1 video
     */
    public function apiDetail($id)
    {
        $video = $this->videoModel->find($id);

        if (! $video) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Video tidak ditemukan',
                ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->withHlsUrl($video),
        ]);
    }

    /**
     * GET /api/videos/{id}/stream
     * Stream via ID
     */
    public function apiStream($id)
    {
        $video = $this->videoModel->find($id);

        if (! $video) {
            return $this->response->setStatusCode(404);
        }

        return redirect()->to(
            base_url('video/stream/' . $video['filename'])
        );
    }

    public function delete($id)
    {
        $video = $this->videoModel->find($id);

        if (! $video) {
            return redirect()->back()->with('error', 'Video tidak ditemukan');
        }

        // 1. Hapus file MP4
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . basename((string) $video['filename']);
        if (is_file($path)) {
            @unlink($path);
        }

        // 2. Hapus folder HLS
        $this->hlsConverter->delete((int) $video['id']);

        // 3. Hapus video (daftar_play & video_views ikut terhapus via CASCADE)
        $this->videoModel->delete($id);

        // 4. Hapus playlist yang kosong
        $db = \Config\Database::connect();

        $emptyPlaylists = $db->query("
            SELECT p.id
            FROM playlist p
            LEFT JOIN daftar_play dp ON dp.playlist_id = p.id
            WHERE dp.id IS NULL
        ")->getResultArray();

        foreach ($emptyPlaylists as $p) {
            $db->table('playlist')->where('id', $p['id'])->delete();
        }

        return redirect()->to(base_url('/video'))
            ->with('success', 'Video & relasi berhasil dihapus');
    }

    /**
     * Server-side DataTables untuk daftar video admin.
     */
    public function dataTable()
    {
        $draw   = (int) ($this->request->getGet('draw') ?? 0);
        $start  = max(0, (int) ($this->request->getGet('start') ?? 0));
        $length = (int) ($this->request->getGet('length') ?? 10);

        if (! in_array($length, [10, 25, 50, 100, -1], true)) {
            $length = 10;
        }

        $searchParam = $this->request->getGet('search');
        $search      = trim((string) (is_array($searchParam) ? ($searchParam['value'] ?? '') : ''));

        $orderParam  = $this->request->getGet('order');
        $orderColumn = 1;
        $orderDir    = 'asc';

        if (is_array($orderParam) && isset($orderParam[0])) {
            $orderColumn = (int) ($orderParam[0]['column'] ?? 1);
            $orderDir    = strtolower((string) ($orderParam[0]['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        }

        // Hanya kolom ini yang boleh dipakai untuk sorting.
        $orderMap = [
            1 => 'title',
            2 => 'created_at',
        ];
        $orderBy = $orderMap[$orderColumn] ?? 'id';

        $db    = \Config\Database::connect();
        $total = $db->table('videos')->countAllResults();

        $builder = $db->table('videos');

        if ($search !== '') {
            $builder->like('title', $search);
        }

        $filtered = $builder->countAllResults(false);

        $builder->orderBy($orderBy, strtoupper($orderDir));

        if ($length !== -1) {
            $builder->limit($length, $start);
        }

        $rows = $builder->get()->getResultArray();
        $data = [];

        foreach ($rows as $i => $video) {
            $filename = (string) $video['filename'];

            $action = '<button type="button" class="btn btn-sm btn-primary js-play-video me-1"'
                . ' data-filename="' . esc($filename, 'attr') . '">'
                . '<i class="bi bi-play-fill"></i> View</button>'
                . '<a href="' . esc(base_url('video/delete/' . $video['id'])) . '"'
                . ' class="btn btn-sm btn-danger"'
                . ' onclick="return confirm(\'Hapus video dan semua relasinya?\')">'
                . '<i class="bi bi-trash"></i> Hapus</a>';

            $data[] = [
                'no'         => $start + $i + 1,
                'name'       => esc((string) $video['title']),
                'created_at' => esc((string) $video['created_at']),
                'action'     => $action,
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ]);
    }

    /**
     * Tambahkan URL HLS absolut bila tersedia.
     */
    protected function withHlsUrl(array $video): array
    {
        $video['hls_url'] = null;

        if (! empty($video['hls_path'])) {
            $video['hls_url'] = base_url('video/hls/' . $video['id'] . '/index.m3u8');
        }

        return $video;
    }

    protected function ensureDirectory(string $path): void
    {
        if (! is_dir($path)) {
            @mkdir($path, 0775, true);
        }
    }
}
