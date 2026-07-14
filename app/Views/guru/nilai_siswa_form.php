<?php
$hasNumberType = false;
$isAllCheckbox = true;
if (!empty($kriteria)) {
    foreach ($kriteria as $k) {
        $t = $k['input_type'] ?? 'number';
        if ($t === 'number') $hasNumberType = true;
        if ($t !== 'checkbox') $isAllCheckbox = false;
    }
}
$kd = $kriteriaData ?? [];
$kategoriId = $kategori['id'];

$totalCount = count($kriteria);
$filledCount = 0;
foreach ($kriteria as $k) {
    $kId = $k['id'];
    $inputType = $k['input_type'] ?? 'number';
    $prev = $kd[$kId] ?? [];
    if ($inputType === 'number' && isset($prev['nilai']) && $prev['nilai'] !== '') $filledCount++;
    elseif ($inputType === 'checkbox' && !empty($prev['selesai'])) $filledCount++;
    elseif ($inputType === 'text' && !empty($prev['huruf'])) $filledCount++;
}
$progressPercent = $totalCount > 0 ? round(($filledCount / $totalCount) * 100) : 0;
?>

<form method="post" action="<?= base_url('guru/nilai/save') ?>" id="formNilai-<?= $kategoriId ?>" class="tab-form-nilai" data-kategori="<?= $kategoriId ?>" data-hitung-kosong="<?= $kategori['hitung_kosong'] ?? 0 ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>"/>
    <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>"/>
    <input type="hidden" name="rombel_id" value="<?= $rombel['id'] ?>"/>
    <input type="hidden" name="kategori_id" value="<?= $kategoriId ?>"/>

    <?php if (!empty($kriteria)): ?>
    <h5 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-2">
        <span class="material-symbols-outlined text-sm text-outline">checklist</span>
        <?= esc($displayName ?? $kategori['nama']) ?>
    </h5>
    <div class="flex items-center gap-2 mb-3 px-1">
        <span class="text-xs text-outline">Progres:</span>
        <div class="flex-1 h-2 bg-surface-container-low rounded-full overflow-hidden" style="max-width: 200px;">
            <div id="progress-<?= $kategoriId ?>" class="h-full rounded-full transition-all duration-500"
                 style="width: <?= $progressPercent ?>%; background: <?= $progressPercent === 100 ? '#10b981' : '#6366f1' ?>;"></div>
        </div>
        <span class="text-xs font-bold <?= $filledCount === $totalCount ? 'text-emerald-600' : 'text-primary' ?>">
            <?= $filledCount ?>/<?= $totalCount ?>
        </span>
    </div>
    <div class="overflow-hidden rounded-xl border-2 border-outline/20">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-5 py-3.5 font-label-md text-label-md text-outline uppercase w-12">No</th>
                    <th class="px-5 py-3.5 font-label-md text-label-md text-outline uppercase">Kriteria</th>
                    <th class="px-5 py-3.5 font-label-md text-label-md text-outline uppercase">Nilai</th>
                    <th class="px-5 py-3.5 font-label-md text-label-md text-outline uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php $no = 1; foreach ($kriteria as $k):
                    $kId = $k['id'];
                    $inputType = $k['input_type'] ?? 'number';
                    $prev = $kd[$kId] ?? [];
                ?>
                <tr class="hover:bg-primary/5 transition-all duration-200">
                    <td class="px-5 py-3 text-sm text-outline"><?= $no++ ?></td>
                    <td class="px-5 py-3">
                        <span class="font-bold text-sm text-on-surface"><?= esc($k['nama']) ?></span>
                        <?php if ($inputType === 'number'): ?>
                        <span class="text-[10px] text-outline ml-2">(Bobot: <?= $k['bobot'] ?>%, Max: <?= $k['skala_max'] ?>)</span>
                        <?php elseif ($inputType === 'checkbox'): ?>
                        <span class="text-[10px] text-outline ml-2">(Centang jika selesai)</span>
                        <?php else: ?>
                        <span class="text-[10px] text-outline ml-2">(Isi nilai huruf)</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <?php if ($inputType === 'number'): ?>
                        <div class="flex items-center gap-2">
                            <input type="number" name="kriteria[<?= $kId ?>][nilai]"
                                   min="0" max="<?= $k['skala_max'] ?>"
                                   data-bobot="<?= $k['bobot'] ?>"
                                   data-skala="<?= $k['skala_max'] ?>"
                                   data-kategori="<?= $kategoriId ?>"
                                   value="<?= $prev['nilai'] ?? '' ?>"
                                   class="kriteria-input w-20 px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm text-center font-bold outline-none transition-all duration-200 focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white hover:border-primary/30"
                                   placeholder="0"/>
                            <span class="text-[10px] text-outline">/ <?= $k['skala_max'] ?></span>
                        </div>
                        <?php elseif ($inputType === 'text'): ?>
                        <input type="text" name="kriteria[<?= $kId ?>][huruf]"
value="<?= esc($prev['huruf'] ?? '') ?>"
                                class="w-28 px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm text-center outline-none transition-all duration-200 focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white hover:border-primary/30"
                                placeholder="cth: Mumtaz"/>
                        <?php elseif ($inputType === 'checkbox'): ?>
                        <label for="cb-selesai-<?= $kId ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg cursor-pointer transition-all duration-200 hover:bg-primary/10">
                            <input type="checkbox" name="kriteria[<?= $kId ?>][selesai]" value="1" id="cb-selesai-<?= $kId ?>"
                                   <?= !empty($prev['selesai']) ? 'checked' : '' ?>
                                   data-kategori="<?= $kategoriId ?>"
                                   class="kriteria-checkbox w-4 h-4 rounded border-outline-variant text-primary transition-all duration-200 focus:ring-primary/30 focus:ring-2"/>
                            <span class="text-sm text-on-surface font-semibold">Selesai</span>
                        </label>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <input type="text" name="kriteria[<?= $kId ?>][catatan]"
value="<?= esc($prev['catatan'] ?? '') ?>"
                                class="w-full min-w-[80px] px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-primary/20 focus:border-primary/40 focus:bg-white hover:border-primary/30"
                                placeholder="Catatan..."/>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-primary/5 font-bold">
                    <td colspan="2" class="px-5 py-4 text-sm text-on-surface">Nilai Akhir</td>
                    <td class="px-5 py-4" colspan="2">
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-outline">Angka:</span>
                            <input type="number" name="nilai" id="nilaiTotal-<?= $kategoriId ?>"
                                   min="0" max="100"
                                   class="w-20 px-3 py-2 bg-white border-2 border-primary/20 rounded-lg text-sm text-center font-bold text-primary outline-none transition-all duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20"
                                   placeholder="0" readonly/>
                            <span class="text-sm text-outline">Huruf:</span>
                            <span id="predikatDisplay-<?= $kategoriId ?>" class="px-4 py-2 rounded-lg text-sm font-bold bg-surface-container-low text-on-surface-variant transition-all duration-200">—</span>
                            <input type="hidden" name="predikat" id="predikatHidden-<?= $kategoriId ?>"/>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php else: ?>
    <div class="space-y-4 p-6 bg-surface-container-low/30 rounded-xl">
        <div>
            <label for="nilaiTotal-<?= $kategoriId ?>" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Nilai</label>
            <input type="number" name="nilai" id="nilaiTotal-<?= $kategoriId ?>" min="0" max="100"
                   class="w-32 px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm text-center font-bold outline-none transition-all duration-200 focus:ring-2 focus:ring-primary/20 focus:border-primary/40 hover:border-primary/30"
                   placeholder="0"/>
        </div>
        <div>
            <label for="catatan-<?= $kategoriId ?>" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Catatan</label>
            <textarea name="catatan" id="catatan-<?= $kategoriId ?>" rows="2" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none transition-all duration-200 focus:ring-2 focus:ring-primary/20 focus:border-primary/40 hover:border-primary/30" placeholder="Catatan (opsional)"></textarea>
        </div>
    </div>
    <?php endif; ?>


    <div class="flex justify-end mt-5">
        <button type="button" onclick="simpanFormNilai(<?= $kategoriId ?>)" id="btnSimpanNilai-<?= $kategoriId ?>"
                class="btn-primary py-2.5 px-6 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">save</span>
            Simpan
        </button>
    </div>
</form>

<!-- Riwayat Nilai -->
<?php if (!empty($progres)): ?>
<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5 mt-6">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <h4 class="font-bold text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-sm text-primary">history</span>
            Riwayat Nilai — <?= esc($kategori['nama']) ?>
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/30">
                    <th class="px-5 py-3 font-label-md text-label-md text-outline uppercase">Tanggal</th>
                    <th class="px-5 py-3 font-label-md text-label-md text-outline uppercase">Nilai</th>
                    <th class="px-5 py-3 font-label-md text-label-md text-outline uppercase">Predikat</th>
                    <th class="px-5 py-3 font-label-md text-label-md text-outline uppercase">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($progres as $p): ?>
                <tr class="transition-all duration-200 hover:bg-primary/5">
                    <td class="px-5 py-3 text-sm text-on-surface-variant"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                    <td class="px-5 py-3 font-bold text-on-surface"><?= $p['nilai'] ?? '—' ?></td>
                    <td class="px-5 py-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold inline-block transition-all duration-200
                            <?= match($p['predikat'] ?? '') {
                                'A' => 'bg-primary-container/20 text-primary',
                                'B' => 'bg-secondary-container/20 text-secondary',
                                'C' => 'bg-surface-container-low text-on-surface-variant',
                                'D' => 'bg-status-offline/10 text-status-offline',
                                'E' => 'bg-error-container/20 text-error',
                                default => 'bg-surface-container-low text-on-surface-variant',
                            } ?>">
                            <?= esc($p['predikat'] ?? '—') ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-outline"><?= esc($p['catatan'] ?: '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
