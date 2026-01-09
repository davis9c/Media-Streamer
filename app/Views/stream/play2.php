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

    <script>
        const videos = <?= json_encode($videos) ?>;
        const container = document.getElementById('videoContainer');

        // Tentukan jumlah kolom grid otomatis
        const count = videos.length;
        const cols = Math.ceil(Math.sqrt(count));
        const rows = Math.ceil(count / cols);

        container.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
        container.style.gridTemplateRows = `repeat(${rows}, 1fr)`;

        videos.forEach(video => {
            const v = document.createElement('video');
            v.src = '/video/stream/' + video.filename;
            v.autoplay = true;
            v.loop = true;
            v.muted = true; // wajib agar autoplay jalan
            v.controls = false;
            v.style.width = '100%';
            v.style.height = '100%';
            v.style.objectFit = 'cover';

            container.appendChild(v);
        });

        // FULLSCREEN otomatis
        function goFullscreen() {
            if (container.requestFullscreen) {
                container.requestFullscreen();
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