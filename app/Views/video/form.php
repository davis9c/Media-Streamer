<div class="col-12 col-xl-4">
    <div class="card admin-card">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 fw-semibold">
                <i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload Video
            </h2>
        </div>

        <div class="card-body">
            <video id="player" class="w-100 rounded border bg-black mb-3" controls style="min-height: 180px;">
                <source id="source" src="" type="video/mp4">
                Browser ini tidak mendukung pemutaran video.
            </video>

            <?php if (isset($validation)): ?>
                <div class="alert alert-danger">
                    <?= $validation->listErrors(); ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('video/upload') ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="videoTitle" class="form-label">Judul Video</label>
                    <input type="text" class="form-control" id="videoTitle" name="title"
                        placeholder="Judul video" required>
                </div>

                <div class="mb-3">
                    <label for="videoFile" class="form-label">File Video</label>
                    <input class="form-control" type="file" id="videoFile" name="video" accept="video/mp4" required>
                    <div class="form-text">Format MP4, maksimal 2 GB.</div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function playVideo(filename) {
        var source = document.getElementById('source');
        var player = document.getElementById('player');

        source.src = <?= json_encode(base_url('video/stream/')) ?> + filename;
        player.load();

        if (player.play) {
            var result = player.play();

            if (result && typeof result.catch === 'function') {
                result.catch(function () {});
            }
        }
    }
</script>
