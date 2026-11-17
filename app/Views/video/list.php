<div class="col-12 col-xl-8">
    <div class="card admin-card">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 fw-semibold">
                <i class="bi bi-camera-video me-2 text-primary"></i>Daftar Video
            </h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle w-100" id="videoTable"
                    data-url="<?= base_url('video/datatable') ?>">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 70px;">No</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col" style="width: 200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
