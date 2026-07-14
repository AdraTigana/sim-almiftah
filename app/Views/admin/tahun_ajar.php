<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Tahun Ajaran<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Tahun Ajar'],
    ]
]) ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="font-headline-sm text-headline-sm text-primary">Tahun Ajaran</h3>
        <p class="text-on-surface-variant text-sm">Kelola tahun ajaran pondok pesantren</p>
    </div>
    <button onclick="showCreateTa()" class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary/20 transition-all">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Tahun Ajaran
    </button>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5" id="taTableCard">
    <div class="px-6 py-5 border-b border-outline-variant/20 flex items-center justify-between">
        <h3 class="font-headline-sm text-headline-sm text-primary">Daftar Tahun Ajaran</h3>
        <div class="flex items-center gap-3">
            <select id="filterTaStatus" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
            <span class="text-xs text-on-surface-variant bg-surface-container-low px-3 py-1 rounded-full"><?= count($tahunAjar) ?> data</span>
        </div>
    </div>
        <div class="divide-y divide-outline-variant/10">
            <?php if (!empty($tahunAjar)): foreach ($tahunAjar as $t): ?>
            <div class="ta-row px-6 py-4 flex items-center justify-between hover:bg-white/40 transition-colors"
                 data-id="<?= $t['id'] ?>"
                  data-aktif="<?= $t['is_current'] ? 1 : 0 ?>">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-lg">calendar_month</span>
                    </div>
                    <div>
                        <p class="font-bold text-on-surface"><?= esc($t['tahun']) ?></p>
                        <p class="text-xs text-on-surface-variant">
                            <?php if ($t['is_current']): ?>
                            <span class="ml-2 px-2 py-0.5 bg-primary-container text-on-primary-container rounded-full font-bold text-[10px]">Aktif</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if (!$t['is_current']): ?>
                    <button onclick="setActiveTa(<?= $t['id'] ?>)"
                            class="min-h-[44px] min-w-[44px] px-3 py-1.5 bg-primary-container/20 text-primary rounded-lg text-xs font-bold hover:bg-primary-container/40 transition-colors">
                        Set Aktif
                    </button>
                    <?php endif; ?>
                    <button onclick="showEditTa(<?= $t['id'] ?>, <?= htmlspecialchars(json_encode($t['tahun'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)"
                            class="p-2 min-h-[44px] min-w-[44px] hover:bg-primary/10 rounded-lg text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button onclick="confirmDeleteTa(<?= $t['id'] ?>, <?= htmlspecialchars(json_encode($t['tahun'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)"
                            class="p-2 min-h-[44px] min-w-[44px] text-error hover:bg-error/10 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada tahun ajaran</div>
            <?php endif; ?>
        </div>
</div>

<!-- Create Modal -->
<div id="createTaModal" role="dialog" aria-modal="true" aria-labelledby="modal-create-ta-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreateTa()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-create-ta-title" class="font-headline-sm text-headline-sm text-primary mb-4">Tambah Tahun Ajaran</h4>
        <form id="createTaForm" class="space-y-4" onsubmit="return submitTaCreate(event)">
            <?= csrf_field() ?>
            <div>
                <label for="taTahun" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Tahun Ajaran</label>
                <input type="text" name="tahun" id="taTahun" required placeholder="contoh: 2025/2026"
                       class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideCreateTa()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editTaModal" role="dialog" aria-modal="true" aria-labelledby="modal-ta-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideEditTa()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-ta-title" class="font-headline-sm text-headline-sm text-primary mb-4">Edit Tahun Ajaran</h4>
        <form id="editTaForm" class="space-y-4" onsubmit="return submitTaEdit(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="editTaId">
            <div>
                <label for="editTaTahun" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Tahun Ajaran</label>
                <input type="text" name="tahun" id="editTaTahun" required
                       class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none"/>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Update</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideEditTa()">Batal</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// --- Filter ---
function filterTa() {
    const aktif = document.getElementById('filterTaStatus')?.value || '';
    document.querySelectorAll('.ta-row').forEach(row => {
        const matchAktif = !aktif || row.dataset.aktif === aktif;
        row.style.display = matchAktif ? '' : 'none';
    });
}
['filterTaStatus'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', filterTa);
});

// --- Create ---
function showCreateTa() {
    document.getElementById('createTaForm').reset();
    document.getElementById('createTaModal').classList.remove('hidden');
    document.getElementById('createTaModal').classList.add('flex');
}
function hideCreateTa() {
    document.getElementById('createTaModal').classList.add('hidden');
    document.getElementById('createTaModal').classList.remove('flex');
}

function offlineSaveTa(form, url) {
    const obj = {};
    new FormData(form).forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
    savePendingAdmin('POST', url, obj).then(function() { safeNotify('Success', 'Data tersimpan lokal.'); });
    safeUnblock('#taTableCard');
}

function submitTaCreate(e) {
    e.preventDefault();
    const form = document.getElementById('createTaForm');
    if (!navigator.onLine) { offlineSaveTa(form, '<?= base_url('admin/tahun-ajar/create') ?>'); return false; }
    const data = new FormData(form);
    safeBlock('#taTableCard');
    fetch('<?= base_url('admin/tahun-ajar/create') ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => { if (res.success) { safeNotify('Success', res.message || 'Tahun ajaran ditambahkan'); hideCreateTa(); setTimeout(() => location.reload(), 1000); } else { safeNotify('Failure', res.message || 'Gagal menyimpan'); } })
        .catch(function() { offlineSaveTa(form, '<?= base_url('admin/tahun-ajar/create') ?>'); })
        .finally(() => safeUnblock('#taTableCard'));
    return false;
}

// --- Edit ---
function showEditTa(id, tahun) {
    document.getElementById('editTaId').value = id;
    document.getElementById('editTaTahun').value = tahun;
    document.getElementById('editTaModal').classList.remove('hidden');
    document.getElementById('editTaModal').classList.add('flex');
}
function hideEditTa() {
    document.getElementById('editTaModal').classList.add('hidden');
    document.getElementById('editTaModal').classList.remove('flex');
}
function submitTaEdit(e) {
    e.preventDefault();
    const id = document.getElementById('editTaId').value;
    const form = document.getElementById('editTaForm');
    if (!navigator.onLine) { offlineSaveTa(form, '<?= base_url('admin/tahun-ajar/edit') ?>/' + id); return false; }
    const data = new FormData(form);
    safeBlock('#taTableCard');
    fetch('<?= base_url('admin/tahun-ajar/edit') ?>/' + id, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => { if (res.success) { safeNotify('Success', res.message || 'Tahun ajaran diupdate'); hideEditTa(); setTimeout(() => location.reload(), 1000); } else { safeNotify('Failure', res.message || 'Gagal mengupdate'); } })
        .catch(function() { offlineSaveTa(form, '<?= base_url('admin/tahun-ajar/edit') ?>/' + id); })
        .finally(() => safeUnblock('#taTableCard'));
    return false;
}

// --- Set Active (Toggle) ---
function setActiveTa(id) {
    if (!navigator.onLine) {
        savePendingAdmin('POST', '<?= base_url('admin/tahun-ajar/set-active') ?>/' + id, { id: id }).then(function() {
            safeNotify('Success', 'Permintaan tersimpan lokal.');
        });
        return;
    }
    safeBlock('#taTableCard');
    fetch('<?= base_url('admin/tahun-ajar/set-active') ?>/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
        .then(r => r.json())
        .then(res => { if (res.success) { safeNotify('Success', 'Tahun Ajar Diaktifkan'); setTimeout(() => location.reload(), 1000); } else { safeNotify('Failure', res.message || 'Gagal mengaktifkan'); } })
        .catch(() => savePendingAdmin('POST', '<?= base_url('admin/tahun-ajar/set-active') ?>/' + id, { id: id }).then(function() { safeNotify('Success', 'Permintaan tersimpan lokal.'); }))
        .finally(() => safeUnblock('#taTableCard'));
}

// --- Delete ---
function confirmDeleteTa(id, tahun) {
    safeConfirm('Hapus Tahun Ajaran?', 'Yakin ingin menghapus <b>' + tahun + '</b>?', 'Ya, Hapus', 'Batal', function() {
        if (!navigator.onLine) {
            savePendingAdmin('POST', '<?= base_url('admin/tahun-ajar/delete') ?>/' + id, { id: id }).then(function() { safeNotify('Success', 'Permintaan hapus tersimpan lokal.'); });
            return;
        }
        safeBlock('#taTableCard');
        fetch('<?= base_url('admin/tahun-ajar/delete') ?>/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
            .then(r => r.json())
            .then(res => { if (res.success) { safeNotify('Success', res.message || 'Tahun ajaran dihapus'); const row = document.querySelector('.ta-row[data-id="' + id + '"]'); if (row) row.remove(); } else { safeNotify('Failure', res.message || 'Gagal menghapus'); } })
            .catch(() => savePendingAdmin('POST', '<?= base_url('admin/tahun-ajar/delete') ?>/' + id, { id: id }).then(function() { safeNotify('Success', 'Permintaan hapus tersimpan lokal.'); }))
            .finally(() => safeUnblock('#taTableCard'));
    });
}
</script>
<?= $this->endSection() ?>