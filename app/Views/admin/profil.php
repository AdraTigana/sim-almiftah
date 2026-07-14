<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Profil<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('admin')], ['label' => 'Profil']]]) ?>
<?php if (session()->getFlashdata('message')): ?>
<div class="p-4 bg-primary-container/20 rounded-xl text-primary text-sm flex items-center gap-2 mb-4">
    <span class="material-symbols-outlined text-sm">check_circle</span>
    <?= esc(session()->getFlashdata('message')) ?>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="p-4 bg-error-container/20 rounded-xl text-error text-sm flex items-center gap-2 mb-4">
    <span class="material-symbols-outlined text-sm">error</span>
    <?= esc(session()->getFlashdata('error')) ?>
</div>
<?php endif; ?>

<div class="max-w-2xl mx-auto">
    <div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
        <div class="bg-gradient-to-r from-primary to-primary-container px-8 py-10 text-center text-on-primary">
            <div class="w-20 h-20 rounded-full bg-white/20 mx-auto mb-4 flex items-center justify-center text-white text-3xl font-bold ring-4 ring-white/30">
                <?= esc(strtoupper(substr($user['nama'] ?? 'A', 0, 1))) ?>
            </div>
            <h3 class="font-headline-md text-headline-md text-white"><?= esc($user['nama'] ?? '—') ?></h3>
            <p class="text-white/80 text-sm capitalize mt-1"><?= esc($user['role_nama'] ?? '') ?></p>
        </div>

        <form method="post" action="<?= base_url('admin/profil/update') ?>" data-offline="true" class="p-8 space-y-6">
            <?= csrf_field() ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fieldNama" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Nama</label>
                    <input type="text" name="nama" id="fieldNama" value="<?= esc($user['nama'] ?? '') ?>" required
                           class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div>
                    <label for="fieldEmail" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Email</label>
                    <input type="email" name="email" id="fieldEmail" value="<?= esc($user['email'] ?? '') ?>" required
                           class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div>
                    <label class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Username</label>
                    <input type="text" value="<?= esc($user['email'] ?? '—') ?>" readonly disabled
                           class="w-full px-3 py-2.5 bg-surface-container-low/50 border-2 border-outline/30 rounded-xl text-sm text-outline cursor-not-allowed"/>
                </div>
                <div>
                    <label class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Role</label>
                    <input type="text" value="<?= esc($user['role_kode'] ?? '—') ?>" readonly disabled
                           class="w-full px-3 py-2.5 bg-surface-container-low/50 border-2 border-outline/30 rounded-xl text-sm text-outline cursor-not-allowed capitalize"/>
                </div>
            </div>

            <div class="border-t border-outline-variant/20 pt-6">
                <h4 class="font-bold text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm text-primary">lock</span>
                    Ubah Password (kosongkan jika tidak diubah)
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="fieldPassword" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Password Lama</label>
                        <input type="password" name="password" id="fieldPassword" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    </div>
                    <div>
                        <label for="fieldPasswordBaru" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Password Baru</label>
                        <input type="password" name="password_baru" id="fieldPasswordBaru" minlength="6" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
