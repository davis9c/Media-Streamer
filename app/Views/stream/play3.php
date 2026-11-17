<!DOCTYPE html>
<html>

<head>
    <title><?= esc($playlist['playlistname']) ?></title>
</head>

<body style="margin:0;background:black;">

    <div id="videoContainer"
        style="width:100vw;height:100vh;display:grid;gap:2px;background:black;">
    </div>

    <div id="emptyState"
        style="display:none;width:100vw;height:100vh;align-items:center;justify-content:center;color:#bbb;font-family:Arial,sans-serif;font-size:18px;">
        Playlist ini belum memiliki video.
    </div>

    <div id="hint"
        style="
        position:fixed;
        bottom:10px;
        left:50%;
        transform:translateX(-50%);
        background:rgba(0,0,0,.6);
        color:white;
        padding:8px 14px;
        font-size:14px;
        cursor:pointer;
        z-index:10;">
        Klik layar untuk aktifkan suara
    </div>

    <script src="<?= base_url('assets/hls/hls.min.js') ?>"></script>
    <script>
        const videos = <?= json_encode($videos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const mp4Base = <?= json_encode(base_url('video/stream/')) ?>;

        const container = document.getElementById('videoContainer');
        const hint = document.getElementById('hint');
        const emptyState = document.getElementById('emptyState');

        let videoElements = [];
        let interactionDone = false;

        const count = videos.length;

        // Guard playlist kosong
        if (count === 0) {
            container.style.display = 'none';
            hint.style.display = 'none';
            emptyState.style.display = 'flex';
        } else {
            let cols = Math.ceil(Math.sqrt(count));
            let rows = Math.ceil(count / cols);

            // Video utama menempati 2x2, jadi butuh area tambahan 3 sel.
            const isOdd = count % 2 === 1 && count > 1;
            if (isOdd) {
                cols = Math.ceil(Math.sqrt(count + 3));
                rows = Math.ceil((count + 3) / cols);
            }

            container.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
            container.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

            initVideos(isOdd);
        }

        function initVideos(isOdd) {
            videos.forEach((video, index) => {

                // WRAPPER
                const wrap = document.createElement('div');
                wrap.style.position = 'relative';
                wrap.style.width = '100%';
                wrap.style.height = '100%';
                wrap.style.background = 'black';

                // LOADING
                const loader = document.createElement('div');
                loader.innerHTML = '&#9203;';
                loader.style.position = 'absolute';
                loader.style.top = '50%';
                loader.style.left = '50%';
                loader.style.transform = 'translate(-50%, -50%)';
                loader.style.fontSize = '32px';
                loader.style.color = 'white';
                loader.style.zIndex = '2';

                // VIDEO
                const v = document.createElement('video');
                v.autoplay = true;
                v.loop = true;
                v.muted = true;
                v.playsInline = true;
                v.controls = false;

                v.style.width = '100%';
                v.style.height = '100%';
                v.style.objectFit = 'cover';
                v.style.cursor = 'pointer';

                // VIDEO UTAMA (GANJIL)
                if (isOdd && index === 0) {
                    wrap.style.gridColumn = 'span 2';
                    wrap.style.gridRow = 'span 2';
                    v.dataset.main = '1';

                    v.addEventListener('playing', () => {
                        setTimeout(initInteraction, 500);
                    });
                }

                // SUMBER: HLS bila ada, fallback MP4
                attachSource(v, video);

                // VIDEO EVENTS
                v.addEventListener('loadstart', () => loader.style.display = 'block');
                v.addEventListener('waiting', () => loader.style.display = 'block');
                v.addEventListener('canplay', () => loader.style.display = 'none');
                v.addEventListener('playing', () => loader.style.display = 'none');

                // AUDIO CONTROL MANUAL
                v.onclick = () => activateAudio(v);

                wrap.appendChild(v);
                wrap.appendChild(loader);
                container.appendChild(wrap);

                videoElements.push(v);
            });
        }

        function attachSource(videoElement, video) {
            const playAttempt = () => videoElement.play().catch(() => {});

            if (video.hls_url) {
                if (window.Hls && window.Hls.isSupported()) {
                    const hls = new Hls({ maxBufferLength: 30 });
                    hls.loadSource(video.hls_url);
                    hls.attachMedia(videoElement);
                    hls.on(Hls.Events.MANIFEST_PARSED, playAttempt);
                    hls.on(Hls.Events.ERROR, (event, data) => {
                        if (data && data.fatal) {
                            // Fallback ke MP4 bila HLS gagal.
                            hls.destroy();
                            videoElement.src = mp4Base + video.filename;
                            playAttempt();
                        }
                    });
                    videoElement._hls = hls;

                    return;
                }

                // Safari/iOS mendukung HLS secara native.
                videoElement.src = video.hls_url;

                return;
            }

            videoElement.src = mp4Base + video.filename;
        }

        // AUDIO HANDLER
        function activateAudio(activeVideo) {
            videoElements.forEach(v => v.muted = true);
            activeVideo.muted = false;
            activeVideo.volume = 1;
            hint.style.display = 'none';
        }

        // FULLSCREEN & AUDIO INIT (SAFE)
        function initInteraction() {
            if (interactionDone) return;
            interactionDone = true;

            if (container.requestFullscreen) {
                container.requestFullscreen().catch(() => {});
            }

            if (videoElements.length > 0) {
                activateAudio(videoElements[0]);
            }
        }

        // FALLBACK USER INTERACTION
        document.addEventListener('click', initInteraction, { once: true });
        document.addEventListener('touchstart', initInteraction, { once: true });
        document.addEventListener('keydown', initInteraction, { once: true });
    </script>

</body>

</html>
