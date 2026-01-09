<div class="col-lg-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Form Video
            </h6>
        </div>

        <div class="card-body">
            <video id="player" controls width="100%">
                <source id="source" src="" type="video/mp4">
            </video>



            <script>
                function playVideo(filename) {
                    const source = document.getElementById('source');
                    const player = document.getElementById('player');

                    source.src = '/video/stream/' + filename;
                    player.load();
                    player.play();
                }
            </script>
            <?php if (isset($validation)): ?>
                <div class="alert alert-danger">
                    <?= $validation->listErrors(); ?>
                </div>
            <?php endif; ?>

            <h2>Upload Video</h2>

            <?php if (session()->getFlashdata('success')): ?>
                <p style="color:green"><?= session()->getFlashdata('success') ?></p>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <p style="color:red"><?= session()->getFlashdata('error') ?></p>
            <?php endif; ?>

            <form action="/video/upload" method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Judul video" required>
                <input type="file" name="video" accept="video/mp4" required>
                <button type="submit">Upload</button>
            </form>

        </div>
    </div>
</div>