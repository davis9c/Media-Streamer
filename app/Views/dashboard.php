<?= $this->extend('layout/dashboard') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-body-secondary mb-0">Ringkasan statistik streaming.</p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="card shadow-sm border-start border-4 border-primary h-100">
            <div class="card-body">
                <div class="text-uppercase small fw-semibold text-primary mb-1">Total Views</div>
                <div class="display-6 fw-bold text-body"><?= (int) $totalViews ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <?= $this->include('dashboard/list') ?>
</div>

<?= $this->endSection() ?>
