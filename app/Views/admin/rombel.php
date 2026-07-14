<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Rombel<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Rombel'],
    ]
]) ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="font-headline-sm text-headline-sm text-primary">Rombel</h3>
        <p class="text-on-surface-variant text-sm">Kelola rombel dan wali kelas</p>
    </div>
    <button onclick="showCreateRombel()" class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary/20 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Rombel
    </button>
</div>

<div class="space-y-6" id="rombelList">
    <div class="flex items-center gap-3 flex-wrap">
        <select id="filterTa" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
            <option value="">Semua Tahun Ajar</option>
            <?php foreach ($tahunAjar as $ta): ?>
            <option value="<?= $ta['id'] ?>"><?= esc($ta['tahun']) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" id="searchRombel" placeholder="Cari nama rombel..."
               class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20 w-56" aria-label="Cari nama rombel"/>
        <select id="filterAktif" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
            <option value="">Semua Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </select>
    </div>

    <?php if (!empty($rombel)): foreach ($rombel as $r): ?>
    <?php
    $tahunAjarNama = $r['tahun_ajar_nama'] ?? $r['tahun_ajar'] ?? '';
    $kelas = $r['kelas'] ?? '';
    $aktif = $r['is_active'] ?? 1;
    $walasNama = $r['walas_nama'] ?? '';
    $santriCount = $r['santri_count'] ?? 0;
    ?>
    <div class="rombel-card glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5"
         data-id="<?= $r['id'] ?>"
         data-ta="<?= $r['tahun_ajar_id'] ?? '' ?>"
         data-kelas="<?= esc(strtolower($kelas)) ?>"
         data-aktif="<?= $aktif ?>">
        <div class="p-6 flex items-center justify-between">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary"><?= esc($r['nama']) ?></h3>
                <p class="text-xs text-on-surface-variant">
                    <?= esc($tahunAjarNama) ?> • <?= esc($kelas ?: '-') ?> • <?= $santriCount ?> santri
                    <?php if ($walasNama): ?> • Wali: <span class="font-bold text-primary"><?= esc($walasNama) ?></span><?php endif; ?>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="showEditRombel(<?= $r['id'] ?>)" class="p-2 min-h-[44px] min-w-[44px] hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Edit">
                    <span class="material-symbols-outlined text-sm">edit</span>
                </button>
                <button onclick="confirmDeleteRombel(<?= $r['id'] ?>, <?= htmlspecialchars(json_encode($r['nama'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 min-h-[44px] min-w-[44px] text-error hover:bg-error/10 rounded-lg transition-colors" title="Hapus">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="glass-card rounded-3xl p-8 text-center text-on-surface-variant text-sm">Belum ada rombel</div>
    <?php endif; ?>
</div>

<!-- Create Rombel Modal -->
<div id="createRombelModal" role="dialog" aria-modal="true" aria-labelledby="modal-create-rombel-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreateRombel()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-create-rombel-title" class="font-headline-sm text-headline-sm text-primary mb-4">Tambah Rombel</h4>
        <form id="createRombelForm" class="space-y-4" onsubmit="return submitCreateRombel(event)">
            <?= csrf_field() ?>
            <div>
                <label for="rombelNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Rombel</label>
                <input type="text" name="nama" id="rombelNama" required placeholder="Nama Rombel" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none"/>
            </div>
            <div>
                <label for="rombelTahunAjar" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Tahun Ajaran</label>
                <select name="tahun_ajar_id" id="rombelTahunAjar" required class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjar as $ta): ?>
                    <option value="<?= $ta['id'] ?>"><?= esc($ta['tahun']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="rombelKelas" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Kelas</label>
                <input type="text" name="kelas" id="rombelKelas" placeholder="contoh: 1A"
                       class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none"/>
            </div>
            <div>
                <label for="rombelWalas" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Wali Kelas</label>
                <select name="walas_id" id="rombelWalas" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="">Pilih Wali Kelas</option>
                    <?php foreach ($walasList as $w): ?>
                    <option value="<?= $w['id'] ?>"><?= esc($w['nama']) ?> (<?= esc($w['nip'] ?? '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
        </form>
    </div>
</div>

<!-- Edit Rombel Modal -->
<div id="editRombelModal" role="dialog" aria-modal="true" aria-labelledby="modal-rombel-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideEditRombel()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-rombel-title" class="font-headline-sm text-headline-sm text-primary mb-4">Edit Rombel</h4>
        <form id="editRombelForm" class="space-y-4" onsubmit="return submitEditRombel(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="editId">
            <div>
                <label for="editNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Rombel</label>
                <input type="text" name="nama" id="editNama" required class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none"/>
            </div>
            <div>
                <label for="editTahunAjarId" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Tahun Ajaran</label>
                <select name="tahun_ajar_id" id="editTahunAjarId" required class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="">Pilih Tahun Ajaran</option>
                    <?php foreach ($tahunAjar as $ta): ?>
                    <option value="<?= $ta['id'] ?>"><?= esc($ta['tahun']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="editKelas" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Kelas</label>
                <input type="text" name="kelas" id="editKelas" placeholder="contoh: 1A"
                       class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none"/>
            </div>
            <div>
                <label for="editWalasId" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Wali Kelas</label>
                <select name="walas_id" id="editWalasId" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="">Pilih Wali Kelas</option>
                    <?php foreach ($walasList as $w): ?>
                    <option value="<?= $w['id'] ?>"><?= esc($w['nama']) ?> (<?= esc($w['nip'] ?? '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="editIsActive" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Status</label>
                <select name="is_active" id="editIsActive" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn-primary w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
        </form>
    </div>
</div>

<!-- Assign Walas Modal -->
<div id="assignWalasModal" role="dialog" aria-modal="true" aria-labelledby="modal-assign-walas-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideAssignWalas()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-assign-walas-title" class="font-headline-sm text-headline-sm text-primary mb-4">Atur Wali Kelas</h4>
        <form id="assignWalasForm" class="space-y-4" onsubmit="return submitAssignWalas(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="rombel_id" id="assignWalasRombelId">
            <p class="text-sm text-on-surface-variant" id="assignWalasInfo">Rombel: </p>
            <div>
                <label for="assignWalasSelect" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Wali Kelas</label>
                <select name="walas_id" id="assignWalasSelect" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none">
                    <option value="">— Tidak ada wali kelas —</option>
                    <?php foreach ($walasList as $w): ?>
                    <option value="<?= $w['id'] ?>"><?= esc($w['nama']) ?> (<?= esc($w['nip'] ?? '-') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary w-full py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// --- Filter rombel ---
function filterRombel() {
    const ta = document.getElementById('filterTa')?.value || '';
    const query = (document.getElementById('searchRombel')?.value || '').toLowerCase();
    const aktif = document.getElementById('filterAktif')?.value || '';
    document.querySelectorAll('.rombel-card').forEach(card => {
        const matchTa = !ta || card.dataset.ta === ta;
        const matchNama = !query || card.textContent.toLowerCase().includes(query);
        const matchAktif = !aktif || card.dataset.aktif === aktif;
        card.style.display = matchTa && matchNama && matchAktif ? '' : 'none';
    });
}
['filterTa', 'searchRombel', 'filterAktif'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', filterRombel);
    if (id === 'searchRombel') document.getElementById(id)?.addEventListener('keyup', filterRombel);
});

// --- Create Rombel ---
function showCreateRombel() {
    document.getElementById('createRombelForm').reset();
    document.getElementById('createRombelModal').classList.remove('hidden');
    document.getElementById('createRombelModal').classList.add('flex');
}
function hideCreateRombel() {
    document.getElementById('createRombelModal').classList.add('hidden');
    document.getElementById('createRombelModal').classList.remove('flex');
}

function offlineSaveRombel(form, url) {
    const obj = {};
    new FormData(form).forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
    savePendingAdmin('POST', url, obj).then(function() {
        safeNotify('Success', 'Data rombel tersimpan lokal.');
    });
    safeUnblock('#rombelList');
}

function submitCreateRombel(e) {
    e.preventDefault();
    if (!navigator.onLine) { offlineSaveRombel(e.target, '<?= base_url('admin/rombel/create') ?>'); return false; }
    const data = new FormData(e.target);
    safeBlock('#rombelList');
    fetch('<?= base_url('admin/rombel/create') ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message); hideCreateRombel(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal menyimpan'); }
        })
        .catch(function() { offlineSaveRombel(e.target, '<?= base_url('admin/rombel/create') ?>'); })
        .finally(() => safeUnblock('#rombelList'));
    return false;
}

// --- Edit Rombel ---
function showEditRombel(id) {
    fetch('<?= base_url('admin/rombel/get') ?>/' + id)
        .then(r => r.json())
        .then(data => {
            document.getElementById('editId').value = data.id || '';
            document.getElementById('editNama').value = data.nama || '';
            document.getElementById('editTahunAjarId').value = data.tahun_ajar_id || '';
            document.getElementById('editKelas').value = data.kelas || '';
            document.getElementById('editWalasId').value = data.walas_id || '';
            document.getElementById('editIsActive').value = data.is_active ?? 1;
            document.getElementById('editRombelModal').classList.remove('hidden');
            document.getElementById('editRombelModal').classList.add('flex');
        });
}
function hideEditRombel() { toggleRombelModal(false); }
function toggleRombelModal(show) {
    const el = document.getElementById('editRombelModal');
    el.classList.toggle('hidden', !show);
    el.classList.toggle('flex', show);
}

function submitEditRombel(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    if (!navigator.onLine) { offlineSaveRombel(e.target, '<?= base_url('admin/rombel/update') ?>/' + id); return false; }
    const data = new FormData(e.target);
    safeBlock('#rombelList');
    fetch('<?= base_url('admin/rombel/update') ?>/' + id, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message); hideEditRombel(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal mengupdate'); }
        })
        .catch(function() { offlineSaveRombel(e.target, '<?= base_url('admin/rombel/update') ?>/' + id); })
        .finally(() => safeUnblock('#rombelList'));
    return false;
}

// --- Delete Rombel ---
function confirmDeleteRombel(id, nama) {
    safeConfirm('Hapus Rombel?', 'Yakin ingin menghapus <b>' + nama + '</b>?', 'Ya, Hapus', 'Batal', () => {
        if (!navigator.onLine) {
            savePendingAdmin('POST', '<?= base_url('admin/rombel/delete') ?>/' + id, { id: id }).then(function() {
                safeNotify('Success', 'Permintaan hapus tersimpan lokal.');
            });
            return;
        }
        safeBlock('#rombelList');
        fetch('<?= base_url('admin/rombel/delete') ?>/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.json())
            .then(res => {
                if (res.success) { safeNotify('Success', res.message); const card = document.querySelector('.rombel-card[data-id="' + id + '"]'); if (card) card.remove(); }
                else { safeNotify('Failure', res.message); }
            })
            .catch(() => savePendingAdmin('POST', '<?= base_url('admin/rombel/delete') ?>/' + id, { id: id }).then(function() { safeNotify('Success', 'Permintaan hapus tersimpan lokal.'); }))
            .finally(() => safeUnblock('#rombelList'));
    });
}

// --- Assign Walas ---
function showAssignWalas(rombelId, walasNama) {
    document.getElementById('assignWalasRombelId').value = rombelId;
    document.getElementById('assignWalasInfo').textContent = 'Rombel: ' + (document.querySelector('.rombel-card[data-id="' + rombelId + '"] h3')?.textContent || '');
    document.getElementById('assignWalasSelect').value = '';
    if (walasNama) {
        fetch('<?= base_url('admin/rombel/get-walas') ?>/' + rombelId)
            .then(r => r.json())
            .then(data => {
                if (data.walas_id) document.getElementById('assignWalasSelect').value = data.walas_id;
            })
            .catch(() => {});
    }
    document.getElementById('assignWalasModal').classList.remove('hidden');
    document.getElementById('assignWalasModal').classList.add('flex');
}
function hideAssignWalas() {
    document.getElementById('assignWalasModal').classList.add('hidden');
    document.getElementById('assignWalasModal').classList.remove('flex');
}

function submitAssignWalas(e) {
    e.preventDefault();
    const rombelId = document.getElementById('assignWalasRombelId').value;
    const data = new FormData(e.target);
    if (!navigator.onLine) {
        const obj = {};
        data.forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
        savePendingAdmin('POST', '<?= base_url('admin/rombel/assign-walas') ?>/' + rombelId, obj).then(function() {
            safeNotify('Success', 'Penugasan tersimpan lokal.'); hideAssignWalas();
        });
        return false;
    }
    safeBlock('#assignWalasModal .glass-card');
    fetch('<?= base_url('admin/rombel/assign-walas') ?>/' + rombelId, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message); hideAssignWalas(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal menyimpan'); }
        })
        .catch(() => safeNotify('Failure', 'Gagal menyimpan penugasan'))
        .finally(() => safeUnblock('#assignWalasModal .glass-card'));
    return false;
}
</script>
<?= $this->endSection() ?>
