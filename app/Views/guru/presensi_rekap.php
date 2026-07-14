<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rekap Kehadiran<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_guru') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php if ($rombelId && $mapelId): ?>
<?= view('components/_breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => base_url('guru')],
    ['label' => 'Kelas Saya', 'url' => base_url('guru/input-saya')],
    ['label' => ($mapel['nama'] ?? '') . ' — ' . ($rombelNama ?? '')],
    ['label' => 'Rekap Kehadiran'],
]]) ?>
<div class="flex items-center gap-3 text-sm mb-4">
    <a href="<?= base_url('guru/nilai/mapel/' . $mapelId . '/kelas/' . $rombelId . '?tab=presensi') ?>" class="inline-flex items-center gap-1 text-primary">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
</div>
<?php else: ?>
<?= view('components/_breadcrumb', ['items' => [
    ['label' => 'Dashboard', 'url' => base_url('guru')],
    ['label' => 'Rekap Kehadiran'],
]]) ?>
<?php endif; ?>

<div class="mb-6">
    <h3 class="font-headline-sm text-headline-sm text-primary">Rekap Kehadiran</h3>
    <p class="text-on-surface-variant text-sm">Matriks kehadiran santri per tanggal<?= $mapel ? ' — ' . esc($mapel['nama']) : '' ?></p>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <form method="get" action="<?= base_url('guru/presensi/rekap') ?>" class="flex flex-wrap gap-4 items-end">
            <?php if ($mapelId): ?>
            <input type="hidden" name="mapel_id" value="<?= $mapelId ?>"/>
            <?php endif; ?>
            <div class="min-w-[200px]">
                <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1 block">Pilih Rombel</label>
                <select name="rombel_id" onchange="this.form.submit()"
                        class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">— Pilih Rombel —</option>
                    <?php foreach ($rombel as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $rombelId == $r['id'] ? 'selected' : '' ?>><?= esc($r['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <?php if (!empty($siswaList) && !empty($dates)): ?>
    <div class="px-6 py-3 border-b border-outline-variant/10 flex gap-6 text-xs font-bold">
        <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-primary-container/30 text-primary text-center leading-5">H</span> Hadir</span>
        <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-surface-variant text-outline text-center leading-5">S</span> Sakit</span>
        <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-surface-variant text-outline text-center leading-5">I</span> Izin</span>
        <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-error-container/30 text-error text-center leading-5">A</span> Alpha</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase w-12 sticky left-0 bg-surface-container-low/50 z-10">No</th>
                    <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase w-28 sticky left-12 bg-surface-container-low/50 z-10">NIS</th>
                    <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase sticky left-40 bg-surface-container-low/50 z-10">Nama Santri</th>
                    <?php foreach ($dates as $date): ?>
                    <th class="px-3 py-4 font-label-md text-label-md text-outline uppercase text-center min-w-[44px] max-w-[44px]" title="<?= date('d/m/Y', strtotime($date)) ?>"><?= date('d/m', strtotime($date)) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php $no = 1; foreach ($siswaList as $s): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="px-5 py-3 text-sm text-outline sticky left-0 bg-white z-10"><?= $no++ ?></td>
                    <td class="px-5 py-3 text-sm text-outline sticky left-12 bg-white z-10"><?= esc($s['nis']) ?></td>
                    <td class="px-5 py-3 font-bold text-sm text-on-surface sticky left-40 bg-white z-10"><?= esc($s['nama']) ?></td>
                    <?php foreach ($dates as $date):
                        $data = $presensiData[$s['siswa_id']][$date] ?? null;
                        $status = $data['status'] ?? null;
                        $keterangan = $data['keterangan'] ?? '';
                    ?>
                    <td class="px-3 py-3 text-center">
                        <?php if ($status): ?>
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold cursor-default transition-all duration-150 hover:scale-110
                            <?= match($status) {
                                'hadir' => 'bg-primary-container/30 text-primary',
                                'sakit', 'izin' => 'bg-surface-variant text-outline',
                                'alpha' => 'bg-error-container/30 text-error',
                                default => 'bg-surface-container-low text-outline',
                            } ?>"
                            <?= $keterangan ? 'title="' . esc($keterangan) . '"' : '' ?>>
                            <?= match($status) {
                                'hadir' => 'H',
                                'sakit' => 'S',
                                'izin'  => 'I',
                                'alpha' => 'A',
                                default => '—',
                            } ?>
                        </span>
                        <?php else: ?>
                        <span class="text-outline/30">—</span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif ($rombelId): ?>
    <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada data presensi untuk rombel ini<?= $mapel ? ' — mapel ' . esc($mapel['nama']) : '' ?></div>
    <?php else: ?>
    <div class="p-8 text-center text-on-surface-variant text-sm">Silakan pilih rombel untuk melihat rekap kehadiran</div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
