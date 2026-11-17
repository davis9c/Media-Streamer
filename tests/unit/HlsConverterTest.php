<?php

use App\Libraries\HlsConverter;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Hls as HlsConfig;

/**
 * @internal
 */
final class HlsConverterTest extends CIUnitTestCase
{
    private HlsConverter $converter;
    private string $sample;

    protected function setUp(): void
    {
        parent::setUp();

        $config              = new HlsConfig();
        $config->ffmpegPath  = '/usr/bin/ffmpeg';
        $this->converter     = new HlsConverter($config);
        $this->sample        = WRITEPATH . 'test-sample.mp4';
    }

    protected function tearDown(): void
    {
        $this->converter->delete(9999);

        if (is_file($this->sample)) {
            @unlink($this->sample);
        }

        parent::tearDown();
    }

    public function testConvertReturnsNullWhenFfmpegMissing(): void
    {
        $config             = new HlsConfig();
        $config->ffmpegPath = '/tidak/ada/ffmpeg';
        $converter          = new HlsConverter($config);

        $this->assertFalse($converter->isAvailable());
        $this->assertNull($converter->convert(9999, $this->sample));
    }

    public function testConvertCreatesHlsPlaylist(): void
    {
        if (! $this->converter->isAvailable()) {
            $this->markTestSkipped('ffmpeg tidak tersedia.');
        }

        $command = sprintf(
            'ffmpeg -y -f lavfi -i testsrc=size=160x120:rate=15 -t 2 -c:v libx264 -preset ultrafast %s >/dev/null 2>&1',
            escapeshellarg($this->sample)
        );
        exec($command, $output, $code);

        if ($code !== 0 || ! is_file($this->sample)) {
            $this->markTestSkipped('Tidak bisa membuat sample video.');
        }

        $result = $this->converter->convert(9999, $this->sample);

        $this->assertSame('hls/9999/index.m3u8', $result);
        $this->assertFileExists(WRITEPATH . $result);
        $this->assertStringContainsString('#EXTM3U', (string) file_get_contents(WRITEPATH . $result));
    }
}
