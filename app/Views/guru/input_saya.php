<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Kelas Saya<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_guru') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('guru')], ['label' => 'Kelas Saya']]]) ?>
<div class="flex items-center justify-between mb-2 flex-wrap gap-4">
    <div>
        <h3 class="font-headline-sm text-headline-sm text-primary">Kelas Saya</h3>
        <p class="text-on-surface-variant text-sm">Pilih mapel & kelas untuk kelola nilai dan presensi santri</p>
    </div>
</div>

<!-- Filter Tahun Ajaran -->
<form method="get" class="mb-6">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-3 flex-wrap">
            <label class="text-xs text-outline font-semibold uppercase tracking-wider">Tahun Ajaran</label>
            <select name="tahun_ajar_id" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20" onchange="this.form.submit()">
                <option value="">Semua</option>
                <?php foreach ($tahunAjar as $ta): ?>
                <option value="<?= $ta['id'] ?>" <?= $selectedTa == $ta['id'] ? 'selected' : '' ?>>
                    <?= esc($ta['tahun']) ?> <?= $ta['is_current'] ? '• Aktif' : '' ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if ($selectedTa): ?>
            <a href="<?= base_url('guru/input-saya') ?>" class="text-xs text-primary">Reset filter</a>
            <?php endif; ?>
        </div>
        <button type="button" onclick="openAturKelasModal()" class="px-4 py-2 bg-primary/10 text-primary rounded-xl text-sm font-semibold hover:bg-primary/20 active:scale-[0.97] transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">tune</span>
            Atur Kelas
        </button>
    </div>
</form>

<?php if (!empty($mapelGrouped)): ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($mapelGrouped as $mg): ?>
    <div class="glass-card rounded-2xl overflow-hidden shadow-sm shadow-primary/5 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="px-5 py-4 border-b border-outline-variant/10">
            <h4 class="font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-lg">menu_book</span>
                <?= esc($mg['mapel_nama']) ?>
            </h4>
            <span class="text-[10px] text-outline uppercase tracking-wider font-semibold"><?= count($mg['rombel']) ?> kelas</span>
        </div>
        <div class="px-5 py-3 space-y-2">
            <?php foreach ($mg['rombel'] as $r): ?>
            <a href="<?= base_url('guru/nilai/mapel/' . $mg['mapel_id'] . '/kelas/' . $r['rombel_id']) ?>"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-surface-container-low/50 hover:bg-primary-container/10 hover:text-primary transition-all duration-200 group">
                <span class="text-sm font-bold text-on-surface-variant group-hover:text-primary transition-colors">
                    <?= esc($r['rombel_nama']) ?>
                    <span class="text-[10px] text-outline/60 ml-1.5 font-normal"><?= esc($r['tahun_ajar_nama'] ?? '') ?></span>
                </span>
                <span class="material-symbols-outlined text-sm text-outline group-hover:text-primary group-hover:translate-x-0.5 transition-all">arrow_forward</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="glass-card rounded-3xl p-8 text-center text-on-surface-variant text-sm">
    <span class="material-symbols-outlined text-5xl mb-3 block">school</span>
    Belum ada penugasan mapel
</div>
<?php endif; ?>

<!-- Modal Atur Kelas -->
<div id="aturKelasModal" role="dialog" aria-modal="true" aria-labelledby="modal-atur-kelas-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)closeAturKelasModal()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h4 id="modal-atur-kelas-title" class="font-headline-sm text-headline-sm text-primary mb-2">Atur Kelas</h4>
        <?php if ($selectedTa): ?>
        <p class="text-sm text-on-surface-variant mb-4">Tahun Ajar: <span class="font-semibold text-on-surface"><?= esc($assignTaName) ?></span></p>
        <form method="post" action="<?= base_url('guru/input-saya/assign') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="tahun_ajar_id" value="<?= $selectedTa ?>">
            <div class="space-y-4 max-h-64 overflow-y-auto border-2 border-outline/20 rounded-xl p-4">
                <?php if (!empty($allMapel) && !empty($allRombel)): ?>
                    <?php foreach ($allMapel as $m): ?>
                    <div>
                        <div class="text-sm font-bold text-on-surface mb-1.5 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            <?= esc($m['nama']) ?>
                        </div>
                        <div class="ml-4 grid grid-cols-2 gap-1.5">
                            <?php foreach ($allRombel as $r): ?>
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-surface-container-low rounded-lg p-1.5 transition-colors">
                                <input type="checkbox" name="assign[<?= $m['id'] ?>][]" value="<?= $r['id'] ?>"
                                    class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30"
                                    <?= isset($currentAssign[$m['id']]) && in_array($r['id'], $currentAssign[$m['id']]) ? 'checked' : '' ?>>
                                <span class="text-xs text-on-surface-variant"><?= esc($r['nama']) ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-on-surface-variant italic">Tidak ada mapel atau kelas tersedia.</p>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="closeAturKelasModal()">Batal</button>
            </div>
        </form>
        <?php else: ?>
        <p class="text-sm text-error italic">Tidak ada tahun ajar aktif.</p>
        <div class="flex gap-3 mt-4">
            <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="closeAturKelasModal()">Tutup</button>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function openAturKelasModal() {
    document.getElementById('aturKelasModal').classList.remove('hidden');
    document.getElementById('aturKelasModal').classList.add('flex');
}
function closeAturKelasModal() {
    document.getElementById('aturKelasModal').classList.remove('flex');
    document.getElementById('aturKelasModal').classList.add('hidden');
}
</script>
<?= $this->endSection() ?>
