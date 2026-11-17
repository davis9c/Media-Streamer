<nav class="navbar navbar-expand admin-topbar bg-white border-bottom sticky-top px-3 px-lg-4">
    <button class="btn btn-outline-secondary d-lg-none me-2" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Buka menu">
        <i class="bi bi-list"></i>
    </button>

    <span class="navbar-brand mb-0 h6 d-lg-none">Admin</span>

    <div class="ms-auto">
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button"
                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="admin-avatar"><i class="bi bi-person-fill"></i></span>
                <span class="d-none d-sm-inline small fw-semibold"><?= esc(session()->get('name') ?? 'Admin') ?></span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
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
        </div>
    </div>
</nav>
