<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Cetak Rapor - <?= esc($rombel['nama'] ?? '') ?><?= $this->endSection() ?>
<?php $rolePrefix = $rolePrefix ?? 'walas'; ?>
<?php $dashboardUrl = $rolePrefix === 'admin' ? base_url('admin') : base_url('walas'); ?>
<?= $this->section('sidebar') ?>
<?php if ($rolePrefix === 'admin'): ?>
<?= $this->include('layouts/sidebar_admin') ?>
<?php else: ?>
<?= $this->include('layouts/sidebar_walas') ?>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => $dashboardUrl], ['label' => 'Cetak Rapor', 'url' => base_url($rolePrefix . '/cetak')], ['label' => ($rombel['nama'] ?? '')]]]) ?>

<div class="flex items-center gap-3 mb-4">
    <a href="<?= base_url($rolePrefix . '/cetak') ?>" class="inline-flex items-center gap-1 text-primary text-sm">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary">Cetak Rapor - <?= esc($rombel['nama'] ?? '') ?></h3>
                <p class="text-xs text-on-surface-variant mt-1"><?= esc($rombel['tahun_ajar_nama'] ?? $rombel['tahun_ajar'] ?? '') ?> • <?= count($siswaList) ?> santri</p>
            </div>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input id="searchCetakSantri" class="pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20 w-56" placeholder="Cari nama/NIS..."/>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase w-12">No</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">Nama Santri</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php if (!empty($siswaList)): $no = 1; ?>
                <?php foreach ($siswaList as $s): ?>
                <tr class="cetak-santri-row hover:bg-white/40 transition-colors"
                    data-search="<?= esc(strtolower($s['nama'] . ' ' . $s['nis'])) ?>">
                    <td class="px-6 py-4 text-on-surface-variant text-sm"><?= $no++ ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface font-medium"><?= esc($s['nis']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"><?= esc(strtoupper(substr($s['nama'], 0, 1))) ?></div>
                            <span class="font-bold text-sm text-on-surface"><?= esc($s['nama']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-center">
                        <a href="<?= base_url($rolePrefix . '/cetak/excel/' . $rombel['id'] . '/' . $s['siswa_id']) ?>"
                           class="btn-primary inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-bold hover:shadow-md active:scale-[0.98] transition-all">
                            <span class="material-symbols-outlined text-sm">download</span>
                            Cetak Rapor
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant/80 text-sm font-medium">Belum ada data santri</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('searchCetakSantri')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.cetak-santri-row').forEach(row => {
        row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
