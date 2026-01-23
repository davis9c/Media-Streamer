<div class="col-lg-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Form Video
            </h6>
        </div>

        <div class="card-body">

            <?php if (session()->getFlashdata('success')): ?>
                <p style="color:green"><?= session()->getFlashdata('success') ?></p>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <p style="color:red"><?= session()->getFlashdata('error') ?></p>
            <?php endif; ?>

            <form action="/playlist/store" method="post">

                <label>Nama Playlist</label><br>
                <input type="text" name="playlistname" required>
                <hr>

                <h3>Pilih Video (Urutan sesuai klik)</h3>

                <div id="videoList">
                    <?php foreach ($videos as $video): ?>
                        <label style="display:block; cursor:pointer;">
                            <input type="checkbox"
                                value="<?= $video['id'] ?>"
                                onchange="handleCheck(this)"><?= $video['id'] ?>.
                            <?= esc($video['title']) ?>
                            <span class="order"></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <input type="hidden" name="videos_order" id="videos_order">
                <script>
                    let selectedVideos = [];

                    function handleCheck(checkbox) {
                        const id = checkbox.value;

                        if (checkbox.checked) {
                            selectedVideos.push(id);
                        } else {
                            selectedVideos = selectedVideos.filter(v => v !== id);
                        }

                        updateOrderUI();
                        document.getElementById('videos_order').value = selectedVideos.join(',');
                    }

                    function updateOrderUI() {
                        document.querySelectorAll('#videoList label').forEach(label => {
                            const checkbox = label.querySelector('input');
                            const span = label.querySelector('.order');
                            const index = selectedVideos.indexOf(checkbox.value);

                            span.textContent = index !== -1 ? ` (Urutan ${index + 1})` : '';
                        });
                    }
                </script>


                <br>
                <button type="submit">Simpan Playlist</button>
            </form>
        </div>
    </div>
</div>