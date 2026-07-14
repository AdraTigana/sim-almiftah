<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rekapitulasi<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_walas') ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('walas')], ['label' => 'Rekapitulasi']]]) ?>
<div class="flex items-center justify-between mb-6">
    <h3 class="font-headline-sm text-headline-sm text-primary">Rekapitulasi Nilai</h3>
    <form method="get" class="flex items-center gap-2">
        <select name="tahun_ajar_id" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20" onchange="this.form.submit()">
            <?php foreach ($tahunAjar as $ta): ?>
            <option value="<?= $ta['id'] ?>" <?= $selectedTaId == $ta['id'] ? 'selected' : '' ?>>
                <?= esc($ta['tahun']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if (!empty($rombelSaya)): ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($rombelSaya as $r): ?>
    <a href="<?= base_url('walas/rekapitulasi/kelas/' . $r['id']) ?>"
       class="glass-card p-6 rounded-2xl hover:bg-white transition-colors group">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined">assessment</span>
            </div>
            <div>
                <h4 class="font-bold text-on-surface"><?= esc($r['nama']) ?></h4>
                <p class="text-xs text-on-surface-variant"><?= esc($r['tahun_ajar_nama'] ?? $r['tahun_ajar'] ?? '') ?></p>
            </div>
        </div>
        <span class="text-primary text-sm font-bold flex items-center gap-1">
            Lihat Rekap <span class="material-symbols-outlined text-sm">arrow_forward</span>
        </span>
    </a>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="text-center py-12 text-on-surface-variant text-sm">Tidak ada rombel untuk tahun ajar ini</div>
<?php endif; ?>
<?= $this->endSection() ?>
