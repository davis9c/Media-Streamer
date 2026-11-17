<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h1 class="h6 mb-0 fw-semibold">
                    <i class="bi bi-cloud-arrow-up me-2 text-primary"></i>Upload Video
                </h1>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/upload') ?>" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="adminVideoFile" class="form-label">File Video</label>
                        <input class="form-control" type="file" id="adminVideoFile" name="video"
                            accept="video/mp4" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-upload me-1"></i>Upload
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
