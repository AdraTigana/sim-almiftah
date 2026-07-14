<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rekapitulasi - <?= esc($rombel['nama'] ?? '') ?><?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_walas') ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('walas')], ['label' => 'Rekapitulasi', 'url' => base_url('walas/rekapitulasi')], ['label' => ($rombel['nama'] ?? '')]]]) ?>
<div class="flex items-center gap-3 mb-4">
    <a href="<?= base_url('walas/rekapitulasi') ?>" class="inline-flex items-center gap-1 text-primary text-sm">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary">Rekapitulasi - <?= esc($rombel['nama']) ?></h3>
                <p class="text-xs text-on-surface-variant mt-1"><?= esc($rombel['tahun_ajar_nama'] ?? '') ?> • <?= count($rekap) ?> mapel</p>
            </div>
        </div>
    </div>

    <?php if (!empty($rekap)): ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">Mapel</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">KKM</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase">Jml</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-center" colspan="2">Rata-rata Nilai</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-center">Tuntas</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-on-surface-variant uppercase text-center">%</th>
                </tr>
                <tr class="bg-surface-container-low/50">
                    <th></th><th></th><th></th>
                    <th class="px-3 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Angka</th>
                    <th class="px-3 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Huruf</th>
                    <th></th><th></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($rekap as $row):
                    $predikat = predikatNilai($row['rata_nilai']);
                ?>
                <tr class="hover:bg-white/40 transition-colors">
                    <td class="px-6 py-4 font-bold text-on-surface"><?= esc($row['mapel']) ?></td>
                    <td class="px-6 py-4 text-sm text-center font-bold text-on-surface"><?= $row['kkm'] ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= $row['jumlah'] ?></td>
                    <td class="px-6 py-4 text-center font-bold <?= $row['rata_nilai'] >= $row['kkm'] ? 'text-primary' : 'text-status-offline' ?>"><?= esc(number_format($row['rata_nilai'], 1)) ?></td>
                    <td class="px-6 py-4 text-center"><span class="px-2.5 py-0.5 rounded text-[10px] font-bold <?= $row['rata_nilai'] >= $row['kkm'] ? 'bg-primary-container/40 text-primary' : 'bg-error-container/40 text-error' ?>"><?= $predikat ?></span></td>
                    <td class="px-6 py-4 text-center text-sm text-primary font-bold"><?= $row['tuntas'] ?>/<?= $row['jumlah'] ?></td>
                    <td class="px-6 py-4 text-center">
                        <?php $pct = $row['jumlah'] > 0 ? round($row['tuntas'] / $row['jumlah'] * 100) : 0; ?>
                        <div class="flex items-center gap-2 justify-center">
                            <div class="w-20 h-2 bg-surface-container-low rounded-full overflow-hidden">
                                <div class="h-full rounded-full <?= $pct >= 70 ? 'bg-primary' : ($pct >= 50 ? 'bg-secondary' : 'bg-status-offline') ?>" style="width: <?= $pct ?>%"></div>
                            </div>
                            <span class="text-xs text-on-surface-variant font-medium"><?= $pct ?>%</span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="p-8 text-center text-on-surface-variant/80 text-sm font-medium">Belum ada data nilai untuk rombel ini</div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
