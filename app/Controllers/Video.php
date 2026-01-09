<?php

namespace App\Controllers;

use App\Models\VideoModel;

class Video extends BaseController
{
    protected $videoModel;

    public function __construct()
    {
        $this->videoModel = new VideoModel();
    }

    // =========================
    // WEB VIEW
    // =========================
    public function index()
    {
        $videos = $this->videoModel->findAll();
        return view('video', ['videos' => $videos]);
    }

    public function upload()
    {
        $video = $this->request->getFile('video');
        $title = $this->request->getPost('title');

        if (!$video || !$video->isValid()) {
            return redirect()->back()
                ->with('error', $video ? $video->getErrorString() : 'File tidak terkirim');
        }

        $allowed = [
            'video/mp4',
            'video/x-m4v',
            'video/quicktime'
        ];

        if (!in_array($video->getMimeType(), $allowed)) {
            return redirect()->back()->with('error', 'Format video tidak didukung');
        }

        $newName = $video->getRandomName();
        $video->move(WRITEPATH . 'uploads', $newName);

        $this->videoModel->insert([
            'title' => $title,
            'filename' => $newName
        ]);

        return redirect()->to(base_url('video'))->with('success', 'Video berhasil diupload');
    }

    // =========================
    // STREAM FILE
    // =========================
    public function stream($filename)
    {
        $path = WRITEPATH . 'uploads/' . $filename;

        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return $this->response
            ->setHeader('Content-Type', 'video/mp4')
            ->setBody(file_get_contents($path));
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
        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->videoModel->findAll()
        ]);
    }

    /**
     * GET /api/videos/{id}
     * Ambil detail 1 video
     */
    public function apiDetail($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status' => false,
                    'message' => 'Video tidak ditemukan'
                ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $video
        ]);
    }

    /**
     * GET /api/videos/{id}/stream
     * Stream via ID
     */
    public function apiStream($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return $this->response->setStatusCode(404);
        }

        return redirect()->to(
            base_url('video/stream/' . $video['filename'])
        );
    }

    public function delete($id)
    {
        $video = $this->videoModel->find($id);

        if (!$video) {
            return redirect()->back()->with('error', 'Video tidak ditemukan');
        }

        // 1. Hapus file fisik
        $path = WRITEPATH . 'uploads/' . $video['filename'];
        if (file_exists($path)) {
            unlink($path);
        }

        // 2. Hapus video (daftar_play ikut terhapus)
        $this->videoModel->delete($id);

        // 3. Hapus playlist yang kosong
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
}
