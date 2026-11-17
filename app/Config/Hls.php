<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi konversi HLS (HTTP Live Streaming).
 *
 * Semua nilai dapat dioverride lewat .env, contoh:
 *   hls.enabled = true
 *   hls.ffmpegPath = /usr/bin/ffmpeg
 *   hls.timeout = 600
 */
class Hls extends BaseConfig
{
    /**
     * Aktifkan konversi HLS otomatis setelah upload.
     */
    public bool $enabled = true;

    /**
     * Lokasi binary ffmpeg.
     */
    public string $ffmpegPath = '/usr/bin/ffmpeg';

    /**
     * Batas waktu proses konversi (detik).
     */
    public int $timeout = 600;

    /**
     * Durasi tiap segmen HLS (detik).
     */
    public int $segmentTime = 6;

    /**
     * Codec video/audio dan kualitas output.
     */
    public string $videoCodec  = 'libx264';
    public string $audioCodec  = 'aac';
    public string $preset      = 'veryfast';
    public int $crf            = 23;
    public string $audioBitrate = '128k';

    /**
     * Folder penyimpanan HLS, relatif terhadap WRITEPATH.
     */
    public string $storagePath = 'hls';
}
