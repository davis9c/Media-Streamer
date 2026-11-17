<!DOCTYPE html>
<html>

<head>
    <title><?= esc($playlist['playlistname']) ?></title>
</head>

<body>

    <h2>Streaming: <?= esc($playlist['playlistname']) ?></h2>

    <video id="player" controls width="100%" muted playsinline></video>

    <hr>

    <h3>Daftar Video</h3>

    <ol>
        <?php foreach ($videos as $i => $video): ?>
            <li>
                <button onclick="playVideo(<?= (int) $i ?>)">
                    <?= esc($video['title']) ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ol>

    <script src="<?= base_url('assets/hls/hls.min.js') ?>"></script>
    <script>
        const videos = <?= json_encode($videos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const mp4Base = <?= json_encode(base_url('video/stream/')) ?>;

        const player = document.getElementById('player');
        let hls = null;

        function playVideo(index) {
            const video = videos[index];
            if (!video) return;

            if (hls) {
                hls.destroy();
                hls = null;
            }

            player.removeAttribute('src');
            player.load();

            if (video.hls_url && window.Hls && window.Hls.isSupported()) {
                hls = new Hls();
                hls.loadSource(video.hls_url);
                hls.attachMedia(player);
                hls.on(Hls.Events.MANIFEST_PARSED, () => player.play().catch(() => {}));
            } else {
                player.src = video.hls_url || (mp4Base + video.filename);
                player.play().catch(() => {});
            }
        }
    </script>

</body>

</html>
