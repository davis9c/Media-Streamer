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
                        <?php foreach ($playlists as $i => $playlist): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($playlist['playlistname']) ?></td>
                                <td><?= esc($playlist['created_at']) ?></td>

                                <td><a href="<?= base_url('/playlist/delete/' . $playlist['id']) ?>"
                                        onclick="return confirm('Hapus playlist ini?')"
                                        class="btn btn-danger btn-sm">
                                        Hapus
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