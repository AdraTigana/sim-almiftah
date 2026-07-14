<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Dashboard Ustadz<?= $this->endSection() ?>

<?= $this->section('sidebar') ?>
<?= $this->include('layouts/sidebar_guru') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard']]]) ?>

<!-- Welcome Header -->
<section class="space-y-2">
    <h3 class="font-headline-md md:text-headline-md text-on-surface">Assalamu'alaikum, <?= esc(session()->get('nama')) ?></h3>
    <p class="text-on-surface-variant font-body-md text-body-md">
        <?= date('l, d F Y') ?>
        <?php if ($tahunAktif): ?>
        <span class="ml-3 px-2 py-0.5 bg-primary-container text-on-primary-container rounded-md text-xs font-bold"><?= esc($tahunAktif['tahun']) ?></span>
        <?php endif; ?>
    </p>
</section>

<!-- Stats Cards -->
<section class="flex gap-6">
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-lg">groups</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Santri Binaan</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $totalSantri ?? 0 ?></p>
    </div>
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-secondary-container/10 flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                <span class="material-symbols-outlined text-lg">school</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Kelas Diampu</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $kelasDiampu ?? 0 ?></p>
    </div>
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-tertiary-container/10 flex items-center justify-center text-tertiary group-hover:bg-tertiary group-hover:text-on-tertiary transition-colors">
                <span class="material-symbols-outlined text-lg">auto_stories</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Progres Pekan Ini</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $progresPekanIni ?? 0 ?></p>
    </div>
</section>

<!-- Main Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Mapel Progress + Recent Activity -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Progres Terakhir -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h5 class="font-headline-sm text-headline-sm text-on-surface">Input Progres Terakhir</h5>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
                    <input id="searchDashboardProgres" class="pl-8 pr-3 py-1.5 bg-surface-container-low border-2 border-outline/60 rounded-lg text-xs outline-none focus:ring-2 focus:ring-primary/20 w-40" placeholder="Cari santri..."/>
                </div>
            </div>
            <div class="glass-card rounded-2xl overflow-hidden divide-y divide-outline-variant/20">
                <?php if (!empty($progresTerakhir)): foreach ($progresTerakhir as $p): ?>
                <div class="p-4 flex items-center gap-4 hover:bg-surface-container-lowest transition-all duration-200 progres-item" data-search="<?= esc(strtolower($p['nama_siswa'])) ?>">
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold text-xs shrink-0"><?= esc(strtoupper(substr($p['nama_siswa'], 0, 2))) ?></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-label-md text-on-surface truncate"><?= esc($p['nama_siswa']) ?></p>
                        <p class="text-on-surface-variant text-[12px] truncate">
                            <?= esc($p['mapel']) ?> <?= esc($p['kategori']) ?>
                            <?php if ($p['rombel']): ?><span class="text-outline/60">• <?= esc($p['rombel']) ?></span><?php endif; ?>
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="block font-label-md text-primary"><?= esc($p['predikat'] ?? '—') ?></span>
                        <span class="text-[10px] text-on-surface-variant"><?= esc($p['waktu']) ?></span>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada progres hari ini</div>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('guru/input-saya') ?>" class="mt-4 block w-full py-3.5 glass-card rounded-2xl border-dashed border-2 border-outline/60 flex items-center justify-center gap-2 text-primary font-bold hover:bg-primary-container/5 transition-all duration-200">
                <span class="material-symbols-outlined text-sm">add_circle</span>
                Tambah Progres Baru
            </a>
        </section>
    </div>

    <!-- Right: Quick Links -->
    <div class="space-y-6">
        <div class="glass-card rounded-2xl p-5">
            <h6 class="font-bold text-on-surface mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">quick_reference</span>
                Kelas Saya
            </h6>
            <div class="space-y-1.5">
                <?php if (!empty($mapelProgress)): ?>
                <?php foreach ($mapelProgress as $mp): ?>
                <?php foreach ($mp['rombel'] as $r): ?>
                <a href="<?= base_url('guru/nilai/mapel/' . $mp['mapel_id'] . '/kelas/' . $r['rombel_id']) ?>"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-surface-container-low transition-colors text-sm group">
                    <span class="w-2 h-2 rounded-full bg-primary/40 group-hover:bg-primary transition-colors"></span>
                    <span class="text-on-surface-variant group-hover:text-primary transition-colors"><?= esc($mp['mapel_nama']) ?></span>
                    <span class="text-[11px] text-outline/60"><?= esc($r['rombel_nama']) ?></span>
                </a>
                <?php endforeach; ?>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="text-sm text-on-surface-variant italic">Belum ada kelas</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($tahunAktif): ?>
        <div class="glass-card rounded-2xl p-5 bg-primary-container/10 border border-primary/10">
            <h6 class="font-bold text-primary mb-1">Tahun Ajaran Aktif</h6>
            <p class="text-lg font-headline-sm text-primary"><?= esc($tahunAktif['tahun']) ?></p>
            <a href="<?= base_url('guru/input-saya') ?>" class="mt-3 inline-flex items-center gap-1 text-xs text-primary font-semibold hover:underline">
                Lihat semua kelas →
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('searchDashboardProgres')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.progres-item').forEach(item => {
        item.style.display = !q || item.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
