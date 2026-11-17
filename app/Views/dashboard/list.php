<div class="col-12 col-xl-6">
    <div class="card admin-card h-100">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 fw-semibold">
                <i class="bi bi-bar-chart-line me-2 text-primary"></i>Top Video
            </h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 70px;">No</th>
                            <th scope="col">Judul Video</th>
                            <th scope="col" class="text-end" style="width: 130px;">Jumlah View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topVideos)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary py-4">Belum ada data view.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($topVideos as $i => $v): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td><?= esc($v['title']) ?></td>
                                <td class="text-end fw-semibold"><?= (int) $v['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="col-12 col-xl-6">
    <div class="card admin-card h-100">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 fw-semibold">
                <i class="bi bi-collection-play me-2 text-primary"></i>View per Playlist
            </h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 70px;">No</th>
                            <th scope="col">Playlist</th>
                            <th scope="col" class="text-end" style="width: 130px;">Jumlah View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($playlistViews)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-body-secondary py-4">Belum ada data view.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($playlistViews as $i => $p): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td><?= esc($p['playlistname']) ?></td>
                                <td class="text-end fw-semibold"><?= (int) $p['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
