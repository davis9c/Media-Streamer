<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Media Streamer Admin">
    <title><?= esc($title ?? 'Admin - XProject') ?></title>

    <link href="<?= base_url('assets/vendor/bootstrap/5.3.9/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/bootstrap-icons/1.13.2/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/datatables/2.3.8/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
</head>

<body class="admin-body">
    <!-- Sidebar -->
    <div class="offcanvas-lg offcanvas-start admin-sidebar" tabindex="-1" id="adminSidebar"
        aria-labelledby="adminSidebarLabel">
        <?= $this->include('partial/sidebar') ?>
    </div>
    <!-- End of Sidebar -->

    <div class="admin-main d-flex flex-column min-vh-100">
        <!-- Topbar -->
        <?= $this->include('partial/topbar') ?>
        <!-- End of Topbar -->

        <!-- Main Content -->
        <main class="container-fluid px-3 px-lg-4 py-4 flex-grow-1">
            <?= $this->renderSection('content') ?>
        </main>
        <!-- End of Main Content -->

        <!-- Footer -->
        <?= $this->include('partial/footer') ?>
        <!-- End of Footer -->
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="logoutModalLabel">Siap untuk Logout?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Pilih "Logout" di bawah jika Anda siap mengakhiri sesi Anda.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a class="btn btn-danger" href="<?= base_url('logout') ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/vendor/jquery/3.7.1/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/5.3.9/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/datatables/2.3.8/jquery.dataTables.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/datatables/2.3.8/dataTables.bootstrap5.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
