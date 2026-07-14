<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rapor - <?= esc($siswa['nama'] ?? '') ?><?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_walas') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('walas')], ['label' => 'Rapor Kelas', 'url' => base_url('walas/rapor')], ['label' => ($rombel['nama'] ?? ''), 'url' => base_url('walas/rapor/kelas/' . $rombel['id'])], ['label' => ($siswa['nama'] ?? '')]]]) ?>

<div class="flex items-center justify-between mb-4">
    <a href="<?= base_url('walas/rapor/kelas/' . $rombel['id']) ?>" class="inline-flex items-center gap-1 text-primary text-sm">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
    <a href="<?= base_url('walas/cetak/excel/' . $rombel['id'] . '/' . $siswa['id']) ?>"
       class="inline-flex items-center gap-1 px-4 py-2 bg-primary text-on-primary rounded-xl text-sm font-bold hover:shadow-lg transition-all">
        <span class="material-symbols-outlined text-sm">print</span>
        Cetak Rapor
    </a>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm"><?= esc(strtoupper(substr($siswa['nama'], 0, 2))) ?></div>
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary"><?= esc($siswa['nama']) ?></h3>
                <p class="text-xs text-on-surface-variant">NIS: <?= esc($siswa['nis']) ?> • <?= esc($rombel['nama'] ?? '') ?> • <?= esc($rombel['tahun_ajar_nama'] ?? '') ?></p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="bg-surface-container-low">
                    <th class="px-4 py-2.5 text-on-surface-variant uppercase w-8">No</th>
                    <th class="px-4 py-2.5 text-on-surface-variant uppercase">Mata Pelajaran</th>
                    <th class="px-4 py-2.5 text-on-surface-variant uppercase">KKM</th>
                    <th colspan="2" class="px-4 py-2.5 text-on-surface-variant uppercase text-center">Pengetahuan</th>
                    <th colspan="2" class="px-4 py-2.5 text-on-surface-variant uppercase text-center">Rerata Kelas</th>
                </tr>
                <tr class="bg-surface-container-low/50">
                    <th></th><th></th><th></th>
                    <th class="px-2 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Angka</th>
                    <th class="px-2 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Huruf</th>
                    <th class="px-2 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Angka</th>
                    <th class="px-2 py-1.5 text-on-surface-variant text-[10px] uppercase text-center font-semibold">Huruf</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/5">
                <?php
                $no = 1;
                $currentGroup = null;
                foreach ($allMapel as $m):
                    $n = $nilaiByMapel[$m['nama']] ?? null;
                    $group = $m['kelompok'] ?? '';
                    $kkm = $m['kkm'] ?? 65;
                    $nilai = $n['nilai'] ?? ($n['nilai_p'] ?? null);
                    $rerata = $rerataByMapel[$m['nama']] ?? null;
                    if ($group !== $currentGroup):
                        $currentGroup = $group;
                ?>
                <tr class="bg-surface-container-low/70">
                    <td colspan="7" class="px-4 py-2 text-xs font-bold text-on-surface-variant uppercase tracking-wider"><?= esc($group) ?></td>
                </tr>
                <?php endif; ?>
                <tr class="hover:bg-white/40">
                    <td class="px-4 py-2 text-on-surface-variant"><?= $no++ ?></td>
                    <td class="px-4 py-2 font-bold text-on-surface"><?= esc($m['nama']) ?></td>
                    <td class="px-4 py-2 text-center font-bold text-on-surface"><?= $kkm ?></td>
                    <td class="px-4 py-2 text-center font-bold <?= $nilai !== null && $nilai >= 70 ? 'text-primary' : ($nilai !== null ? 'text-status-offline' : 'text-on-surface-variant') ?>"><?= $nilai ?? '—' ?></td>
                    <td class="px-4 py-2 text-center"><span class="px-2 py-0.5 rounded text-[10px] font-bold <?= predikatNilai($nilai) !== '—' ? 'bg-primary-container/40 text-primary font-bold' : 'text-on-surface-variant' ?>"><?= predikatNilai($nilai) ?></span></td>
                    <td class="px-4 py-2 text-center font-bold text-on-surface-variant"><?= $rerata !== null ? round((float)$rerata) : '—' ?></td>
                    <td class="px-4 py-2 text-center"><span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-surface-container-low/80 text-on-surface-variant"><?= predikatNilai($rerata) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php $p = $presensi; if ($p && $p['total'] > 0): ?>
    <div class="px-6 py-4 border-t border-outline-variant/10 flex items-center gap-4 text-sm">
        <span class="text-on-surface-variant font-bold uppercase tracking-wider text-xs">Presensi</span>
        <span class="flex items-center gap-1"><span class="px-2.5 py-0.5 rounded-full bg-secondary/90 text-on-secondary font-bold text-xs">S: <?= (int)$p['sakit'] ?></span></span>
        <span class="flex items-center gap-1"><span class="px-2.5 py-0.5 rounded-full bg-surface-variant/80 text-on-surface-variant font-bold text-xs">I: <?= (int)$p['izin'] ?></span></span>
        <span class="flex items-center gap-1"><span class="px-2.5 py-0.5 rounded-full bg-error-container/90 text-on-error-container font-bold text-xs">A: <?= (int)$p['alpha'] ?></span></span>
        <span class="text-on-surface-variant/80 text-xs font-medium">Total: <?= (int)$p['total'] ?> hari</span>
    </div>
    <?php endif; ?>

    <div class="px-6 py-4 border-t border-outline-variant/10 flex items-center justify-between text-xs text-on-surface-variant">
        <span>Mengetahui,</span>
        <span class="font-bold text-primary"><?= esc($namaWalas) ?></span>
    </div>
</div>
<?= $this->endSection() ?>
