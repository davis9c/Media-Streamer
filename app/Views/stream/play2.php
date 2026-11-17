<!DOCTYPE html>
<html>

<head>
    <title><?= esc($playlist['playlistname']) ?></title>
</head>

<body style="margin:0; background:black;">

    <div id="videoContainer"
        style="
        display:grid;
        width:100vw;
        height:100vh;
        gap:2px;
        background:black;
     ">
    </div>

    <script src="<?= base_url('assets/hls/hls.min.js') ?>"></script>
    <script>
        const videos = <?= json_encode($videos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const mp4Base = <?= json_encode(base_url('video/stream/')) ?>;
        const container = document.getElementById('videoContainer');

        // Tentukan jumlah kolom grid otomatis
        const count = videos.length;
        const cols = Math.max(1, Math.ceil(Math.sqrt(count)));
        const rows = Math.max(1, Math.ceil(count / cols));

        container.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
        container.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

        videos.forEach(video => {
            const v = document.createElement('video');
            v.autoplay = true;
            v.loop = true;
            v.muted = true; // wajib agar autoplay jalan
            v.controls = false;
            v.playsInline = true;
            v.style.width = '100%';
            v.style.height = '100%';
            v.style.objectFit = 'cover';

            if (video.hls_url) {
                if (window.Hls && window.Hls.isSupported()) {
                    const hls = new Hls({ maxBufferLength: 30 });
                    hls.loadSource(video.hls_url);
                    hls.attachMedia(v);
                    hls.on(Hls.Events.MANIFEST_PARSED, () => v.play().catch(() => {}));
                    hls.on(Hls.Events.ERROR, (event, data) => {
                        if (data && data.fatal) {
                            hls.destroy();
                            v.src = mp4Base + video.filename;
                            v.play().catch(() => {});
                        }
                    });
                } else {
                    v.src = video.hls_url;
                }
            } else {
                v.src = mp4Base + video.filename;
            }

            container.appendChild(v);
        });

        // FULLSCREEN otomatis
        function goFullscreen() {
            if (container.requestFullscreen) {
                container.requestFullscreen().catch(() => {});
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        }

        document.addEventListener('click', goFullscreen);
        window.onload = goFullscreen;
    </script>

</body>

</html>
