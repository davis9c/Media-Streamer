<?php $segment = service('uri')->getSegment(1); ?>
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= base_url('dashboard') ?>">
            <i class="bi bi-play-btn-fill text-primary"></i>
            <span class="fw-semibold">XProject</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"
            aria-controls="adminNavbar" aria-expanded="false" aria-label="Buka menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= $segment === 'dashboard' ? 'active' : '' ?>" <?= $segment === 'dashboard' ? 'aria-current="page"' : '' ?>
                        href="<?= base_url('dashboard') ?>">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $segment === 'playlist' ? 'active' : '' ?>" <?= $segment === 'playlist' ? 'aria-current="page"' : '' ?>
                        href="<?= base_url('playlist') ?>">
                        <i class="bi bi-music-note-list me-1"></i>Playlist
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $segment === 'video' ? 'active' : '' ?>" <?= $segment === 'video' ? 'aria-current="page"' : '' ?>
                        href="<?= base_url('video') ?>">
                        <i class="bi bi-camera-video me-1"></i>Video
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('stream') ?>" target="_blank" rel="noopener">
                        <i class="bi bi-broadcast me-1"></i>Halaman Stream
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i><?= esc(session()->get('name') ?? 'Admin') ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <button class="dropdown-item text-danger" type="button" data-bs-toggle="modal"
                                data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
