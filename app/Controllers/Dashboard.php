<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // Total views
        $totalViews = $db->table('video_views')->countAll();

        // Top videos
        $topVideos = $db->query("
            SELECT v.title, COUNT(vv.id) AS total
            FROM video_views vv
            JOIN videos v ON v.id = vv.video_id
            GROUP BY vv.video_id
            ORDER BY total DESC
            LIMIT 5
        ")->getResultArray();

        // Views per playlist
        $playlistViews = $db->query("
            SELECT p.playlistname, COUNT(vv.id) AS total
            FROM video_views vv
            JOIN playlist p ON p.id = vv.playlist_id
            GROUP BY vv.playlist_id
        ")->getResultArray();

        return view('dashboard', [
            'totalViews'    => $totalViews,
            'topVideos'     => $topVideos,
            'playlistViews' => $playlistViews
        ]);
    }
}
