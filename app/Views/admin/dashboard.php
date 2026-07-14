<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Dashboard Admin<?= $this->endSection() ?>

<?= $this->section('sidebar') ?>
<?= $this->include('layouts/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard'],
    ]
]) ?>

<!-- Welcome Header -->
<section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="font-headline-lg text-headline-lg text-primary">Ahlan wa Sahlan, <?= esc(session()->get('nama')) ?></h2>
        <p class="text-on-surface-variant mt-1">Berikut adalah ringkasan perkembangan akademik Pesantren Al-Miftah hari ini.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="px-4 py-2 glass-card rounded-xl text-primary font-bold text-sm flex items-center gap-2">
            <span class="material-symbols-outlined">calendar_month</span>
            <?= esc($tahunAktif['tahun'] ?? '—') ?>
        </span>

    </div>
</section>



<!-- Stats Grid -->
<section class="flex gap-6">
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-on-primary transition-colors">
                <span class="material-symbols-outlined text-lg">group</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Total Santri</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $totalSantri ?? 0 ?></p>
    </div>
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-secondary-container/10 flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">
                <span class="material-symbols-outlined text-lg">badge</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Total Guru</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $totalGuru ?? 0 ?></p>
    </div>
    <div class="flex-1 glass-card p-6 rounded-3xl shadow-sm shadow-primary/5 hover:shadow-md transition-shadow group">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-tertiary-container/10 flex items-center justify-center text-tertiary group-hover:bg-tertiary group-hover:text-on-tertiary transition-colors">
                <span class="material-symbols-outlined text-lg">menu_book</span>
            </div>
        </div>
        <h3 class="text-outline font-label-md uppercase tracking-wider">Total Mata Pelajaran</h3>
        <p class="font-headline-lg text-headline-lg text-on-surface mt-1"><?= $totalMapel ?? 0 ?></p>
    </div>
</section>

<!-- Activity Section -->
<section class="grid grid-cols-1 gap-8">
    <div class="glass-card rounded-3xl p-6 shadow-sm shadow-primary/5 flex flex-col">
        <div class="mb-6">
            <h3 class="font-headline-sm text-headline-sm text-primary">Aktivitas Terbaru</h3>
            <p class="text-body-md text-on-surface-variant">Update akademik santri</p>
        </div>
        <div class="flex-1 space-y-4 overflow-y-auto pr-2 scrollbar-hide">
            <?php if (!empty($aktivitas)): foreach ($aktivitas as $a): ?>
            <div class="flex gap-4 p-3 hover:bg-white rounded-2xl transition-all cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-primary-container/20 flex items-center justify-center text-primary shrink-0">
                    <span class="material-symbols-outlined text-sm">trending_up</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-on-surface"><?= esc($a['nama']) ?></p>
                    <p class="text-xs text-on-surface-variant"><?= esc($a['aktivitas']) ?></p>
                </div>
                <span class="text-[10px] text-outline font-medium"><?= $a['waktu'] ?></span>
            </div>
            <?php endforeach; else: ?>
            <div class="text-center py-8 text-on-surface-variant text-sm">Belum ada aktivitas</div>
            <?php endif; ?>
        </div>
        <button class="w-full mt-6 py-2 text-primary font-bold text-sm border-2 border-primary/10 rounded-xl hover:bg-primary hover:text-on-primary transition-all">Lihat Semua Aktivitas</button>
    </div>
</section>

<!-- Table Section -->
<section class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-outline-variant/20">
        <div class="flex items-center gap-3">
            <div class="w-1 h-8 bg-primary rounded-full"></div>
            <h3 class="font-headline-sm text-headline-sm text-on-surface">Update Terakhir Santri</h3>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-full md:w-64">
                <span class="material-symbols-outlined text-sm absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20" placeholder="Cari nama santri..." type="text" id="searchUpdate"/>
            </div>
            <span class="text-sm text-on-surface-variant">Total: <?= count($santriUpdates) ?> santri</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-8 py-4 font-label-md text-label-md text-outline uppercase tracking-wider">Nama Santri</th>
                    <th class="px-8 py-4 font-label-md text-label-md text-outline uppercase tracking-wider">Kategori</th>
                    <th class="px-8 py-4 font-label-md text-label-md text-outline uppercase tracking-wider">Status</th>
                    <th class="px-8 py-4 font-label-md text-label-md text-outline uppercase tracking-wider">Update Terakhir</th>
                    <th class="px-8 py-4 font-label-md text-label-md text-outline uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php if (!empty($santriUpdates)): foreach ($santriUpdates as $s): ?>
                <tr class="update-row hover:bg-white/40 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"><?= esc(strtoupper(substr($s['nama'], 0, 2))) ?></div>
                            <div>
                                <p class="font-bold text-on-surface"><?= esc($s['nama']) ?></p>
                                <p class="text-xs text-on-surface-variant">ID: <?= esc($s['nis']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5"><span class="px-3 py-1 bg-primary-container text-on-primary-container rounded-full text-xs font-bold"><?= esc($s['kategori']) ?></span></td>
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-1 text-primary">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            <span class="text-xs font-bold"><?= esc($s['status']) ?></span>
                        </div>
                    </td>
                    <td class="px-8 py-5"><p class="text-xs text-on-surface"><?= $s['waktu'] ?></p></td>
                    <td class="px-8 py-5 text-right">
                        <button class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">more_horiz</span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="px-8 py-8 text-center text-on-surface-variant text-sm">Belum ada data santri</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('searchUpdate')?.addEventListener('keyup', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.update-row').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
</script>
<?= $this->endSection() ?>