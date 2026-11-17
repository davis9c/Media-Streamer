<?php
$segment = service('uri')->getSegment(1);
$creatorActive = in_array($segment, ['playlist', 'video'], true);
?>
<div class="offcanvas-header d-lg-none">
    <span class="fs-5 fw-semibold text-white" id="adminSidebarLabel">Menu Admin</span>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar"
        aria-label="Tutup"></button>
</div>

<div class="offcanvas-body sidebar-inner p-3">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center mb-3" href="<?= base_url('dashboard') ?>">
        <i class="bi bi-play-btn-fill fs-3 me-2"></i>
        <span class="fs-5 fw-semibold">XProject <small class="text-white-50 fs-6">1.0</small></span>
    </a>

    <hr class="mt-0">

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a class="nav-link <?= $segment === 'dashboard' ? 'active' : '' ?>"
                href="<?= base_url('dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
        </li>

        <li class="mt-3">
            <div class="sidebar-heading">Creator</div>
        </li>

        <li class="nav-item">
            <a class="nav-link <?= $creatorActive ? 'active' : '' ?>" href="#creatorMenu" data-bs-toggle="collapse"
                role="button" aria-expanded="<?= $creatorActive ? 'true' : 'false' ?>" aria-controls="creatorMenu">
                <i class="bi bi-collection-play me-2"></i>Konten
                <i class="bi bi-chevron-down ms-auto small"></i>
            </a>
            <div class="collapse <?= $creatorActive ? 'show' : '' ?>" id="creatorMenu">
                <ul class="nav flex-column sidebar-submenu ms-3 mt-1">
                    <li>
                        <a class="nav-link <?= $segment === 'playlist' ? 'active' : '' ?>"
                            href="<?= base_url('playlist') ?>">
                            <i class="bi bi-music-note-list me-2"></i>Playlist
                        </a>
                    </li>
                    <li>
                        <a class="nav-link <?= $segment === 'video' ? 'active' : '' ?>"
                            href="<?= base_url('video') ?>">
                            <i class="bi bi-camera-video me-2"></i>Video
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item mt-1">
            <a class="nav-link" href="<?= base_url('stream') ?>" target="_blank" rel="noopener">
                <i class="bi bi-broadcast me-2"></i>Halaman Stream
            </a>
        </li>
    </ul>

    <hr>
    <div class="small text-white-50 px-2">Admin Panel &copy; XProject</div>
</div>
