<?php
$count    = count($videos);
$mp4Base  = base_url('video/stream/');
$controls = (bool) (service('request')->getGet('controls') ?? false);

$cols = max(1, (int) ceil(sqrt(max(1, $count))));
$rows = max(1, (int) ceil($count / $cols));

// Video pertama dibuat lebih besar (2x2) bila jumlahnya ganjil.
$hasMain = $count > 1 && $count % 2 === 1;

if ($hasMain) {
    $cols = max(2, (int) ceil(sqrt($count + 3)));
    $rows = max(2, (int) ceil(($count + 3) / $cols));
}

$cells = [];
$skip  = [];
$next  = 0;

if ($hasMain) {
    $cells['0,0'] = 0;
    $skip['0,1']  = true;
    $skip['1,0']  = true;
    $skip['1,1']  = true;
    $next         = 1;
}

for ($r = 0; $r < $rows; $r++) {
    for ($c = 0; $c < $cols; $c++) {
        $key = $r . ',' . $c;

        if (isset($cells[$key]) || isset($skip[$key])) {
            continue;
        }

        if ($next < $count) {
            $cells[$key] = $next;
            $next++;
        }
    }
}

$renderVideo = static function (array $video, bool $showControls) use ($mp4Base): string {
    $mp4 = $mp4Base . (string) $video['filename'];
    $hls = (string) ($video['hls_url'] ?? '');
    $html = '<video class="stream-video" autoplay muted loop playsinline webkit-playsinline'
        . ' preload="auto" data-mp4="' . esc($mp4) . '"'
        . ($showControls ? ' controls' : '')
        . ' onclick="activateAudio(this)">';

    if ($hls !== '') {
        $html .= '<source src="' . esc($hls) . '" type="application/vnd.apple.mpegurl">';
    }

    $html .= '<source src="' . esc($mp4) . '" type="video/mp4">';
    $html .= 'Browser ini tidak mendukung pemutaran video.';
    $html .= '</video>';

    return $html;
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($playlist['playlistname']) ?></title>
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #000;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
        }

        #streamScreen {
            width: 100%;
            height: 100%;
            background: #000;
        }

        .stream-grid {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .stream-grid td {
            padding: 1px;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
        }

        .empty-cell {
            background: #000;
        }

        .stream-video {
            display: block;
            width: 100%;
            height: 100%;
            max-height: 100%;
            background: #000;
            object-fit: cover;
            -o-object-fit: cover;
        }

        .stream-empty {
            display: table;
            width: 100%;
            height: 100%;
            color: #d1d5db;
            font-size: 20px;
            text-align: center;
        }

        .stream-empty span {
            display: table-cell;
            vertical-align: middle;
            padding: 20px;
        }

        .stream-hint {
            position: fixed;
            left: 50%;
            bottom: 14px;
            z-index: 20;
            max-width: 92%;
            padding: 8px 16px;
            background: rgba(0, 0, 0, 0.72);
            border-radius: 6px;
            color: #fff;
            font-size: 14px;
            text-align: center;
            -webkit-transform: translateX(-50%);
            transform: translateX(-50%);
        }

        .stream-controls {
            position: fixed;
            right: 14px;
            bottom: 14px;
            z-index: 21;
        }

        .stream-button {
            display: inline-block;
            min-height: 44px;
            margin-left: 8px;
            padding: 10px 16px;
            border: 1px solid #fff;
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.72);
            color: #fff;
            font-size: 15px;
            line-height: 20px;
            cursor: pointer;
        }

        .stream-button:hover,
        .stream-button:focus {
            background: #fff;
            color: #000;
            outline: 2px solid #ffd54f;
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div id="streamScreen">
        <?php if ($count === 0): ?>
            <div class="stream-empty"><span>Playlist ini belum memiliki video.</span></div>
        <?php else: ?>
            <table class="stream-grid">
                <tbody>
                    <?php for ($r = 0; $r < $rows; $r++): ?>
                        <tr>
                            <?php for ($c = 0; $c < $cols; $c++): ?>
                                <?php $key = $r . ',' . $c; ?>

                                <?php if (isset($skip[$key])): ?>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <?php if (isset($cells[$key])): ?>
                                    <?php
                                    $index   = $cells[$key];
                                    $video   = $videos[$index];
                                    $isMain  = $hasMain && $r === 0 && $c === 0;
                                    ?>
                                    <td<?= $isMain ? ' colspan="2" rowspan="2"' : '' ?>>
                                        <?= $renderVideo($video, $controls || $index === 0) ?>
                                    </td>
                                <?php else: ?>
                                    <td class="empty-cell"></td>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="audioHint" class="stream-hint">
        Klik video atau tombol Suara untuk mengaktifkan audio.
    </div>

    <div class="stream-controls">
        <button type="button" class="stream-button" onclick="activateSound()">Suara</button>
        <button type="button" class="stream-button" onclick="toggleFullscreen()">Layar Penuh</button>
    </div>

    <script>
        (function () {
            'use strict';

            var videos = document.getElementsByTagName('video');
            var hint = document.getElementById('audioHint');

            function activateAudio(activeVideo) {
                for (var i = 0; i < videos.length; i++) {
                    videos[i].muted = true;
                }

                if (activeVideo) {
                    activeVideo.muted = false;

                    try {
                        activeVideo.volume = 1;
                    } catch (error) {
                        // Volume tidak didukung pada sebagian perangkat.
                    }
                }

                if (hint) {
                    hint.style.display = 'none';
                }
            }

            function activateSound() {
                if (videos.length > 0) {
                    activateAudio(videos[0]);
                }
            }

            function fallbackToMp4(videoElement) {
                if (!videoElement || videoElement.getAttribute('data-fallback') === '1') {
                    return;
                }

                var mp4 = videoElement.getAttribute('data-mp4');

                if (!mp4) {
                    return;
                }

                videoElement.setAttribute('data-fallback', '1');
                videoElement.src = mp4;

                if (videoElement.load) {
                    videoElement.load();
                }

                if (videoElement.play) {
                    try {
                        videoElement.play();
                    } catch (error) {
                        // Autoplay bisa diblokir, video tetap bisa diputar manual.
                    }
                }
            }

            function toggleFullscreen() {
                var screenElement = document.getElementById('streamScreen');
                var fullscreenElement = document.fullscreenElement
                    || document.webkitFullscreenElement
                    || document.msFullscreenElement;

                if (fullscreenElement) {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }

                    return;
                }

                if (screenElement.requestFullscreen) {
                    screenElement.requestFullscreen();
                } else if (screenElement.webkitRequestFullscreen) {
                    screenElement.webkitRequestFullscreen();
                } else if (screenElement.msRequestFullscreen) {
                    screenElement.msRequestFullscreen();
                } else if (videos.length > 0 && videos[0].webkitEnterFullscreen) {
                    // Fallback untuk browser TV lama.
                    videos[0].webkitEnterFullscreen();
                }
            }

            window.activateAudio = activateAudio;
            window.activateSound = activateSound;
            window.toggleFullscreen = toggleFullscreen;

            for (var i = 0; i < videos.length; i++) {
                (function (videoElement) {
                    if (videoElement.addEventListener) {
                        videoElement.addEventListener('error', function () {
                            fallbackToMp4(videoElement);
                        }, false);
                    } else if (videoElement.attachEvent) {
                        videoElement.attachEvent('onerror', function () {
                            fallbackToMp4(videoElement);
                        });
                    }
                })(videos[i]);
            }
        })();
    </script>
</body>

</html>
