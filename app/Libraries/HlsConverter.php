<?php

namespace App\Libraries;

use Config\Hls as HlsConfig;

/**
 * Konversi video MP4 menjadi playlist HLS (m3u8 + segmen .ts) memakai ffmpeg.
 *
 * Jika ffmpeg tidak tersedia atau konversi gagal, method convert() mengembalikan
 * null sehingga aplikasi tetap bisa memutar file MP4 asli (fallback).
 */
class HlsConverter
{
    protected HlsConfig $config;

    public function __construct(?HlsConfig $config = null)
    {
        $this->config = $config ?? config('Hls');
    }

    /**
     * Cek apakah ffmpeg tersedia dan eksekusi perintah diizinkan.
     */
    public function isAvailable(): bool
    {
        if (! $this->config->enabled) {
            return false;
        }

        if (! function_exists('exec') || $this->isDisabled('exec')) {
            return false;
        }

        $binary = $this->config->ffmpegPath;

        if ($binary === '' || ! is_file($binary) || ! is_executable($binary)) {
            return false;
        }

        $output = [];
        $code   = 1;
        @exec(escapeshellarg($binary) . ' -version 2>&1', $output, $code);

        return $code === 0;
    }

    /**
     * Konversi satu video ke HLS.
     *
     * @return string|null Path relatif terhadap WRITEPATH, contoh: "hls/12/index.m3u8".
     */
    public function convert(int $videoId, string $sourcePath): ?string
    {
        if ($videoId < 1 || ! is_file($sourcePath) || ! $this->isAvailable()) {
            return null;
        }

        $relativeDir = trim($this->config->storagePath, '/') . '/' . $videoId;
        $outputDir   = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativeDir);

        $this->removeDirectory($outputDir);

        if (! $this->makeDirectory($outputDir)) {
            log_message('error', 'HLS: gagal membuat folder {dir}', ['dir' => $outputDir]);

            return null;
        }

        $playlist = $outputDir . DIRECTORY_SEPARATOR . 'index.m3u8';
        $segments = $outputDir . DIRECTORY_SEPARATOR . 'segment_%03d.ts';

        $command = implode(' ', [
            escapeshellarg($this->config->ffmpegPath),
            '-y',
            '-i ' . escapeshellarg($sourcePath),
            '-c:v ' . escapeshellarg($this->config->videoCodec),
            '-preset ' . escapeshellarg($this->config->preset),
            '-crf ' . escapeshellarg((string) $this->config->crf),
            '-c:a ' . escapeshellarg($this->config->audioCodec),
            '-b:a ' . escapeshellarg($this->config->audioBitrate),
            '-hls_time ' . escapeshellarg((string) $this->config->segmentTime),
            '-hls_playlist_type vod',
            '-hls_segment_filename ' . escapeshellarg($segments),
            escapeshellarg($playlist),
        ]) . ' 2>&1';

        if ($this->hasTimeoutBinary()) {
            $command = 'timeout ' . escapeshellarg((string) $this->config->timeout) . 's ' . $command;
        }

        $output = [];
        $code   = 1;
        @exec($command, $output, $code);

        if ($code !== 0 || ! is_file($playlist)) {
            log_message('error', 'HLS: konversi gagal untuk video {id}: {output}', [
                'id'     => $videoId,
                'output' => implode("\n", array_slice($output, -20)),
            ]);
            $this->removeDirectory($outputDir);

            return null;
        }

        return $relativeDir . '/index.m3u8';
    }

    /**
     * Hapus folder HLS milik satu video.
     */
    public function delete(int $videoId): void
    {
        $relativeDir = trim($this->config->storagePath, '/') . '/' . $videoId;
        $this->removeDirectory(WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, $relativeDir));
    }

    public function storageDirectory(): string
    {
        return WRITEPATH . trim($this->config->storagePath, '/');
    }

    protected function makeDirectory(string $path): bool
    {
        return is_dir($path) || @mkdir($path, 0775, true) || is_dir($path);
    }

    protected function removeDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($path);
    }

    protected function hasTimeoutBinary(): bool
    {
        static $available = null;

        if ($available === null) {
            $output    = [];
            $code      = 1;
            @exec('command -v timeout 2>/dev/null', $output, $code);
            $available = $code === 0 && ! empty($output);
        }

        return $available;
    }

    protected function isDisabled(string $function): bool
    {
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));

        return in_array($function, $disabled, true);
    }
}
