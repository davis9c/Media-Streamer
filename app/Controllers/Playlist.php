<?php

namespace App\Controllers;

use App\Models\PlaylistModel;
use App\Models\DaftarPlayModel;
use App\Models\VideoModel;

class Playlist extends BaseController
{
    protected $playlistModel;
    protected $daftarPlayModel;
    protected $videoModel;

    public function __construct()
    {
        $this->playlistModel   = new PlaylistModel();
        $this->daftarPlayModel = new DaftarPlayModel();
        $this->videoModel      = new VideoModel();
    }

    public function index()
    {
        return view('playlist', [
            'playlists' => $this->playlistModel->orderBy('id', 'DESC')->findAll(),
            'videos'    => $this->videoModel->findAll(),
        ]);
    }

    public function create()
    {
        return view('playlist', [
            'playlists' => $this->playlistModel->findAll(),
            'videos'    => $this->videoModel->findAll(),
        ]);
    }

    public function store()
    {
        $playlistName = trim((string) $this->request->getPost('playlistname'));
        $videosOrder  = $this->request->getPost('videos_order');

        if ($playlistName === '' || ! $videosOrder) {
            return redirect()->back()
                ->with('error', 'Playlist dan video wajib diisi');
        }

        $videoIds = array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $videosOrder)),
            static fn (int $id): bool => $id > 0
        )));

        if ($videoIds === []) {
            return redirect()->back()
                ->with('error', 'Pilih minimal satu video');
        }

        // Pastikan semua video benar-benar ada.
        $existingIds = array_column(
            $this->videoModel->whereIn('id', $videoIds)->findAll(),
            'id'
        );

        if (count($existingIds) !== count($videoIds)) {
            return redirect()->back()
                ->with('error', 'Ada video yang tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // SIMPAN PLAYLIST (AMBIL ID!)
        $playlistId = $this->playlistModel->insert([
            'playlistname' => $playlistName
        ], true);

        if (! $playlistId) {
            $db->transRollback();

            return redirect()->back()
                ->with('error', 'Gagal menyimpan playlist');
        }

        foreach ($videoIds as $position => $videoId) {
            $this->daftarPlayModel->insert([
                'playlist_id' => $playlistId,
                'video_id'    => $videoId,
                'position'    => $position + 1
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan urutan playlist');
        }

        return redirect()->to(base_url('playlist'))
            ->with('success', 'Playlist berhasil dibuat');
    }


    /**
     * Server-side DataTables untuk daftar playlist admin.
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
            1 => 'playlistname',
            2 => 'created_at',
        ];
        $orderBy = $orderMap[$orderColumn] ?? 'id';

        $db    = \Config\Database::connect();
        $total = $db->table('playlist')->countAllResults();

        $builder = $db->table('playlist');

        if ($search !== '') {
            $builder->like('playlistname', $search);
        }

        $filtered = $builder->countAllResults(false);

        $builder->orderBy($orderBy, strtoupper($orderDir));

        if ($length !== -1) {
            $builder->limit($length, $start);
        }

        $rows = $builder->get()->getResultArray();
        $data = [];

        foreach ($rows as $i => $playlist) {
            $action = '<a href="' . esc(base_url('playlist/delete/' . $playlist['id'])) . '"'
                . ' class="btn btn-sm btn-danger"'
                . ' onclick="return confirm(\'Hapus playlist ini?\')">'
                . '<i class="bi bi-trash"></i> Hapus</a>';

            $data[] = [
                'no'         => $start + $i + 1,
                'name'       => esc((string) $playlist['playlistname']),
                'created_at' => esc((string) $playlist['created_at']),
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

    // =====================================
    // 🗑️ DELETE PLAYLIST (ADMIN ONLY)
    // =====================================
    public function delete($id)
    {
        $playlist = $this->playlistModel->find($id);

        if (!$playlist) {
            return redirect()->back()
                ->with('error', 'Playlist tidak ditemukan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // daftar_play akan terhapus otomatis jika FK CASCADE
        // tapi ini aman walaupun CASCADE belum aktif
        $this->daftarPlayModel
            ->where('playlist_id', $id)
            ->delete();

        $this->playlistModel->delete($id);

        $db->transComplete();

        return redirect()->to(base_url('/playlist'))
            ->with('success', 'Playlist berhasil dihapus');
    }
}
