<!DOCTYPE html>
<html>

<head>
    <title><?= esc($playlist['playlistname']) ?></title>
</head>

<body>

    <h2>Streaming: <?= esc($playlist['playlistname']) ?></h2>

    <video id="player" controls width="100%">
        <source id="source" src="" type="video/mp4">
    </video>

    <hr>

    <h3>Daftar Video</h3>

    <ol>
        <?php foreach ($videos as $video): ?>
            <li>
                <button onclick='playVideo("<?= esc($video["filename"], "js") ?>")'>
                    <?= esc($video['title']) ?>
                </button>
            </li>
        <?php endforeach; ?>
    </ol>

    <script>
        function playVideo(filename) {
            const player = document.getElementById('player');
            const source = document.getElementById('source');

            source.src = '/video/stream/' + filename;
            player.load();
            player.play();
        }
    </script>

</body>

</html>