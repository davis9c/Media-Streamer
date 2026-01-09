<div class="col-lg-8">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">T Daftar Video</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <br>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($videos as $i => $video): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($video['title']) ?></td>
                                <td><?= esc($video['created_at']) ?></td>
                                <td>
                                    <button onclick="playVideo('<?= esc($video['filename']) ?>')">
                                        View
                                    </button>
                                    <a href="/video/delete/<?= $video['id'] ?>"
                                        onclick="return confirm('Hapus video dan semua relasinya?')">
                                        🗑 Hapus
                                    </a>

                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>