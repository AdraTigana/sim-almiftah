<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rapor - <?= esc($rombel['nama'] ?? '') ?><?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_walas') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('walas')], ['label' => 'Rapor Kelas', 'url' => base_url('walas/rapor')], ['label' => ($rombel['nama'] ?? '')]]]) ?>
<div class="flex items-center gap-3 mb-4">
    <a href="<?= base_url('walas/rapor') ?>" class="inline-flex items-center gap-1 text-primary text-sm">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary">Rapor Santri - <?= esc($rombel['nama'] ?? '') ?></h3>
                <p class="text-xs text-on-surface-variant mt-1"><?= esc($rombel['tahun_ajar_nama'] ?? $rombel['tahun_ajar'] ?? '') ?> • <?= count($dataSiswa) ?> santri</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                    <input id="searchRaporSantri" class="pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20 w-56" placeholder="Cari nama/NIS..."/>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase w-12">No</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">NIS</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">Nama</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-center" colspan="3">Presensi</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php if (!empty($dataSiswa)): $no = 1; ?>
                <?php foreach ($dataSiswa as $s): ?>
                <tr class="rapor-santri-row hover:bg-white/40 transition-colors"
                    data-search="<?= esc(strtolower($s['nama'] . ' ' . $s['nis'])) ?>">
                    <td class="px-6 py-4 text-on-surface-variant text-sm"><?= $no++ ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface font-medium"><?= esc($s['nis']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"><?= esc(strtoupper(substr($s['nama'], 0, 1))) ?></div>
                            <span class="font-bold text-sm text-on-surface"><?= esc($s['nama']) ?></span>
                        </div>
                    </td>
                    <?php $p = $s['presensi'] ?? null; ?>
                    <td class="px-2 py-4 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-secondary/90 text-on-secondary font-bold text-[11px] min-w-[32px]">S: <?= (int)($p['sakit'] ?? 0) ?></span>
                    </td>
                    <td class="px-2 py-4 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-surface-variant/80 text-on-surface-variant font-bold text-[11px] min-w-[32px]">I: <?= (int)($p['izin'] ?? 0) ?></span>
                    </td>
                    <td class="px-2 py-4 text-center">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-error-container/90 text-on-error-container font-bold text-[11px] min-w-[32px]">A: <?= (int)($p['alpha'] ?? 0) ?></span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= base_url('walas/rapor/siswa/' . $rombel['id'] . '/' . $s['siswa_id']) ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/15 hover:bg-primary/25 text-primary rounded-lg text-xs font-bold transition-colors">
                                <span class="material-symbols-outlined text-sm">visibility</span>
                                Detail
                            </a>
                            <a href="<?= base_url('walas/cetak/excel/' . $rombel['id'] . '/' . $s['siswa_id']) ?>"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-on-primary rounded-lg text-xs font-bold hover:shadow-md active:scale-[0.98] transition-all">
                                <span class="material-symbols-outlined text-sm">print</span>
                                Cetak
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-on-surface-variant/80 text-sm font-medium">Belum ada data santri</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('searchRaporSantri')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.rapor-santri-row').forEach(row => {
        row.style.display = !q || row.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
