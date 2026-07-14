<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Dashboard Wali Kelas<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_walas') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard']]]) ?>
<section class="space-y-2">
    <h3 class="font-headline-md text-headline-md text-on-surface">Assalamu'alaikum, <?= esc(session()->get('nama')) ?></h3>
    <p class="text-on-surface-variant text-sm">Wali Kelas • Tahun Ajaran: <span class="font-bold text-primary"><?= esc($tahunAktif) ?></span></p>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="glass-card p-6 rounded-2xl flex items-center justify-between group hover:bg-primary hover:-translate-y-0.5 transition-all duration-300">
        <div>
            <p class="text-on-surface-variant text-xs uppercase tracking-wider group-hover:text-primary-fixed transition-colors">Total Santri</p>
            <h4 class="font-headline-lg text-headline-lg text-primary group-hover:text-white transition-colors"><?= $totalSantri ?></h4>
        </div>
        <div class="bg-primary/10 p-3 rounded-xl group-hover:bg-white/20 transition-colors">
            <span class="material-symbols-outlined text-primary text-3xl group-hover:text-white">groups</span>
        </div>
    </div>
    <div class="glass-card p-6 rounded-2xl flex items-center justify-between group hover:bg-primary hover:-translate-y-0.5 transition-all duration-300">
        <div>
            <p class="text-on-surface-variant text-xs uppercase tracking-wider group-hover:text-primary-fixed transition-colors">Lulus / Tuntas</p>
            <h4 class="font-headline-lg text-headline-lg text-primary group-hover:text-white transition-colors"><?= $totalLulus ?></h4>
        </div>
        <div class="bg-primary/10 p-3 rounded-xl group-hover:bg-white/20 transition-colors">
            <span class="material-symbols-outlined text-primary text-3xl group-hover:text-white">check_circle</span>
        </div>
    </div>
    <div class="glass-card p-6 rounded-2xl flex items-center justify-between group hover:bg-status-offline hover:-translate-y-0.5 transition-all duration-300">
        <div>
            <p class="text-on-surface-variant text-xs uppercase tracking-wider group-hover:text-white/80 transition-colors">Belum Tuntas</p>
            <h4 class="font-headline-lg text-headline-lg text-status-offline group-hover:text-white transition-colors"><?= $totalBelum ?></h4>
        </div>
        <div class="bg-status-offline/10 p-3 rounded-xl group-hover:bg-white/20 transition-colors">
            <span class="material-symbols-outlined text-status-offline text-3xl group-hover:text-white">pending</span>
        </div>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-6">
    <?php if (!empty($santriBawahKkm)): ?>
    <div class="lg:col-span-2 glass-card rounded-2xl overflow-hidden shadow-sm shadow-primary/5">
        <div class="px-5 py-4 border-b border-outline-variant/20 flex items-center gap-2 bg-status-offline/5">
            <span class="material-symbols-outlined text-status-offline">warning</span>
            <h5 class="font-headline-sm text-headline-sm text-on-surface">Santri di Bawah KKM</h5>
        </div>
        <div class="divide-y divide-outline-variant/10">
            <?php foreach ($santriBawahKkm as $s): ?>
            <div class="px-5 py-3 flex items-center gap-3 hover:bg-white/40 transition-colors">
                <div class="w-8 h-8 rounded-full bg-status-offline/10 flex items-center justify-center text-status-offline font-bold text-xs"><?= esc(strtoupper(substr($s['siswa_nama'], 0, 1))) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-on-surface truncate"><?= esc($s['siswa_nama']) ?></p>
                    <p class="text-xs text-on-surface-variant"><?= esc($s['mapel_nama']) ?> • <?= esc($s['rombel']) ?></p>
                </div>
                <div class="text-right">
                    <span class="block text-sm font-bold text-status-offline"><?= $s['nilai'] ?></span>
                    <span class="text-xs text-on-surface-variant">KKM <?= $s['kkm'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php if (!empty($rataRombel)): ?>
        <div class="glass-card rounded-2xl overflow-hidden shadow-sm shadow-primary/5">
            <div class="px-5 py-4 border-b border-outline-variant/20">
                <h5 class="font-headline-sm text-headline-sm text-primary">Rata-rata Kelas</h5>
            </div>
            <div class="divide-y divide-outline-variant/10">
                <?php foreach ($rataRombel as $rr): ?>
                <div class="px-5 py-3 flex items-center justify-between">
                    <span class="text-sm font-medium text-on-surface"><?= esc($rr['nama']) ?></span>
                    <span class="text-sm font-bold <?= $rr['rata'] !== '—' && $rr['rata'] >= 70 ? 'text-primary' : 'text-status-offline' ?>"><?= $rr['rata'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
    <div class="lg:col-span-2 space-y-6">
        <h5 class="font-headline-sm text-headline-sm text-on-surface">Input Progres Terakhir</h5>
        <div class="glass-card rounded-2xl overflow-hidden divide-y divide-outline-variant/20">
            <?php if (!empty($progresTerakhir)): foreach ($progresTerakhir as $p): ?>
            <div class="p-4 flex items-center gap-4 hover:bg-surface-container-lowest hover:translate-x-0.5 transition-all duration-200 progres-item">
                <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary font-bold"><?= esc(strtoupper(substr($p['nama_siswa'], 0, 2))) ?></div>
                <div class="flex-1">
                    <p class="font-label-md text-on-surface"><?= esc($p['nama_siswa']) ?></p>
                    <p class="text-on-surface-variant text-[12px]"><?= esc($p['mapel'] ?? '—') ?> <?= esc($p['kategori'] ?? '') ?></p>
                </div>
                <div class="text-right">
                    <span class="block font-label-md text-primary"><?= esc($p['predikat'] ?? '—') ?></span>
                    <span class="text-[10px] text-on-surface-variant"><?= esc($p['waktu']) ?></span>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada progres hari ini</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="space-y-6">
        <?php if (!empty($topAlpha)): ?>
        <div class="glass-card rounded-2xl overflow-hidden shadow-sm shadow-primary/5">
            <div class="px-5 py-4 border-b border-outline-variant/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-status-offline">priority_high</span>
                <h5 class="font-headline-sm text-headline-sm text-on-surface">Alpha Tertinggi</h5>
            </div>
            <div class="divide-y divide-outline-variant/10">
                <?php foreach ($topAlpha as $a): ?>
                <div class="px-5 py-3 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-status-offline/10 flex items-center justify-center text-status-offline font-bold text-xs"><?= esc(strtoupper(substr($a['nama'], 0, 1))) ?></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-on-surface truncate"><?= esc($a['nama']) ?></p>
                        <p class="text-xs text-on-surface-variant">Alpha <?= $a['alpha'] ?>/<?= $a['total'] ?> pertemuan</p>
                    </div>
                    <span class="text-sm font-bold text-status-offline"><?= $a['alpha'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($rombelSaya)): foreach ($rombelSaya as $r): ?>
        <div class="glass-card rounded-2xl overflow-hidden shadow-sm shadow-primary/5">
            <div class="p-5 border-b border-outline-variant/20 flex items-center justify-between">
                <div>
                    <h5 class="font-headline-sm text-headline-sm text-primary"><?= esc($r['nama']) ?></h5>
                    <p class="text-xs text-on-surface-variant"><?= $r['jumlah'] ?> santri</p>
                </div>
                <a href="<?= base_url('walas/rapor/kelas/' . $r['id']) ?>" class="text-primary text-sm font-bold flex items-center gap-1">Lihat Rapor <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
            </div>
            <div class="px-5 py-3 flex flex-wrap gap-2">
                <?php foreach (array_slice($r['anggota'], 0, 8) as $a): ?>
                <span class="px-3 py-1 bg-surface-container-low rounded-full text-xs font-medium text-on-surface"><?= esc($a['nama']) ?></span>
                <?php endforeach; ?>
                <?php if (count($r['anggota']) > 8): ?>
                <span class="px-3 py-1 bg-surface-variant rounded-full text-xs text-on-surface-variant">+<?= count($r['anggota']) - 8 ?> lainnya</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; else: ?>
        <div class="glass-card rounded-2xl p-8 text-center text-on-surface-variant text-sm">Belum ada rombel yang ditugaskan</div>
        <?php endif; ?>

        <div class="grid grid-cols-2 gap-3">
            <a href="<?= base_url('walas/rapor') ?>" class="glass-card p-4 rounded-2xl flex items-center gap-3 hover:bg-primary hover:text-on-primary transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary">auto_stories</span>
                <span class="text-sm font-bold text-on-surface group-hover:text-on-primary">Rapor Kelas</span>
            </a>
            <a href="<?= base_url('walas/rekapitulasi') ?>" class="glass-card p-4 rounded-2xl flex items-center gap-3 hover:bg-primary hover:text-on-primary transition-all group">
                <span class="material-symbols-outlined text-primary group-hover:text-on-primary">assessment</span>
                <span class="text-sm font-bold text-on-surface group-hover:text-on-primary">Rekapitulasi</span>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
