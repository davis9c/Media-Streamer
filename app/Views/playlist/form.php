<div class="col-12 col-xl-4">
    <div class="card admin-card">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 fw-semibold">
                <i class="bi bi-plus-circle me-2 text-primary"></i>Form Playlist
            </h2>
        </div>

        <div class="card-body">
            <form action="<?= base_url('playlist/store') ?>" method="post">
                <div class="mb-3">
                    <label for="playlistname" class="form-label">Nama Playlist</label>
                    <input type="text" class="form-control" id="playlistname" name="playlistname"
                        placeholder="Contoh: Playlist Edukasi" required>
                </div>

                <div class="mb-2 d-flex align-items-center justify-content-between">
                    <h3 class="h6 mb-0">Pilih Video</h3>
                    <span class="small text-body-secondary">Urutan sesuai klik</span>
                </div>

                <div id="videoList" class="video-picker border rounded p-2 mb-3">
                    <?php if (empty($videos)): ?>
                        <p class="text-body-secondary small mb-1">Belum ada video. Upload video terlebih dahulu.</p>
                    <?php endif; ?>

                    <?php foreach ($videos as $video): ?>
                        <div class="form-check py-1">
                            <input class="form-check-input playlist-video-check" type="checkbox"
                                value="<?= (int) $video['id'] ?>" id="video-<?= (int) $video['id'] ?>"
                                onchange="handleCheck(this)">
                            <label class="form-check-label d-block" for="video-<?= (int) $video['id'] ?>">
                                <?= (int) $video['id'] ?>. <?= esc($video['title']) ?>
                                <span class="order text-primary fw-semibold"></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="videos_order" id="videos_order">

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save me-1"></i>Simpan Playlist
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    var selectedVideos = [];

    function handleCheck(checkbox) {
        var id = checkbox.value;
        var index = selectedVideos.indexOf(id);

        if (checkbox.checked) {
            if (index === -1) {
                selectedVideos.push(id);
            }
        } else if (index !== -1) {
            selectedVideos.splice(index, 1);
        }

        updateOrderUI();
        document.getElementById('videos_order').value = selectedVideos.join(',');
    }

    function updateOrderUI() {
        var checkboxes = document.querySelectorAll('#videoList .playlist-video-check');

        for (var i = 0; i < checkboxes.length; i++) {
            var checkbox = checkboxes[i];
            var label = checkbox.parentNode.querySelector('.order');
            var index = selectedVideos.indexOf(checkbox.value);

            if (label) {
                label.textContent = index !== -1 ? '(Urutan ' + (index + 1) + ')' : '';
            }
        }
    }
</script>
