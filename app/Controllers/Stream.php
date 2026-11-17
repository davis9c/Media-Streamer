<?php

namespace App\Controllers;

use App\Models\PlaylistModel;
use App\Models\DaftarPlayModel;
use App\Models\VideoModel;

class Stream extends BaseController
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

    // HOME STREAM (DAFTAR PLAYLIST)
    public function index()
    {
        $playlists = $this->playlistModel->findAll();

        return view('stream', [
            'playlists' => $playlists
        ]);
    }

    // STREAM BERDASARKAN PLAYLIST
    public function play($id)
    {
        $playlist = $this->playlistModel->find($id);

        if (!$playlist) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $videos = $this->daftarPlayModel
            ->select('videos.*, daftar_play.position')
            ->join('videos', 'videos.id = daftar_play.video_id')
            ->where('daftar_play.playlist_id', $id)
            ->orderBy('daftar_play.position', 'ASC')
            ->findAll();

        $videos = array_map([$this, 'withHlsUrl'], $videos);

        return view('stream/play3', [
            'playlist' => $playlist,
            'videos'   => $videos
        ]);
    }
    // =========================
    // API STREAM (JSON)
    // =========================

    /**
     * GET /api/stream
     * Daftar playlist
     */
    public function apiListStream()
    {
        $playlists = $this->playlistModel->findAll();

        return $this->response->setJSON([
            'status' => true,
            'data'   => $playlists
        ]);
    }

    /**
     * GET /api/stream/{id}
     * Playlist + daftar video (HLS ready)
     */
    public function apiStream($id)
    {
        $playlist = $this->playlistModel->find($id);

        if (!$playlist) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Playlist tidak ditemukan'
                ]);
        }

        $videos = $this->daftarPlayModel
            ->select('
                videos.id,
                videos.title,
                videos.filename,
                videos.hls_path,
                daftar_play.position
            ')
            ->join('videos', 'videos.id = daftar_play.video_id')
            ->where('daftar_play.playlist_id', $id)
            ->orderBy('daftar_play.position', 'ASC')
            ->findAll();

        $videos = array_map([$this, 'withHlsUrl'], $videos);

        return $this->response->setJSON([
            'status'   => true,
            'playlist' => $playlist,
            'videos'   => $videos
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
}
