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
            'playlists' => $this->playlistModel->findAll(),
            'videos'    => $this->videoModel->findAll(),
        ]);
    }

    public function create()
    {
        return view('playlist', [
            'videos' => $this->videoModel->findAll()
        ]);
    }

    public function store()
    {
        $playlistName = $this->request->getPost('playlistname');
        $videosOrder  = $this->request->getPost('videos_order');

        if (!$playlistName || !$videosOrder) {
            return redirect()->back()
                ->with('error', 'Playlist dan video wajib diisi');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // SIMPAN PLAYLIST (AMBIL ID!)
        $playlistId = $this->playlistModel->insert([
            'playlistname' => $playlistName
        ], true);



        $videoIds = explode(',', $videosOrder);

        foreach ($videoIds as $position => $videoId) {
            $this->daftarPlayModel->insert([
                'playlist_id' => $playlistId,
                'video_id'    => $videoId,
                'position'    => $position + 1
            ]);
        }

        $db->transComplete();

        return redirect()->to('/playlist')
            ->with('success', 'Playlist berhasil dibuat');
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
