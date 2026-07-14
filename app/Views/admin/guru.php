<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Kelola Guru<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Data Guru'],
    ]
]) ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="font-headline-sm text-headline-sm text-primary">Kelola Guru & Pengguna</h3>
        <p class="text-on-surface-variant text-sm">Manajemen akun guru dan penugasan mata pelajaran</p>
    </div>
    <button onclick="showCreate()" class="btn-primary py-2.5 px-5 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add</span>
        Tambah Guru
    </button>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5" id="guruTableCard">
    <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-outline-variant/20">
        <div class="flex items-center gap-3 flex-wrap">
            <select id="filterRole" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Role</option>
                <?php foreach ($roles as $r): ?>
                <option value="<?= esc($r['kode']) ?>"><?= esc($r['nama']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="relative w-full md:w-44">
                <span class="material-symbols-outlined text-sm absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input type="text" id="searchNama" placeholder="Cari nama guru..." class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
        </div>
        <span class="text-sm text-on-surface-variant">Total: <?= count($guru) ?> pengguna</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Nama</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Email</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Role</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase" style="min-width:200px">Mapel Diampu</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10">
                <?php foreach ($guru as $g): ?>
                <tr class="guru-row hover:bg-white/40 transition-colors"
                    data-id="<?= $g['id'] ?>"
                    data-role="<?= esc($g['role_kode']) ?>"
                    data-role="<?= esc($g['role_kode']) ?>">
                    <td class="px-6 py-4 align-top">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"><?= esc(strtoupper(substr($g['nama'], 0, 1))) ?></div>
                            <span class="font-bold text-on-surface"><?= esc($g['nama']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant align-top"><?= esc($g['email']) ?></td>
                    <td class="px-6 py-4 align-top">
                        <span class="px-3 py-1 bg-primary-container text-on-primary-container rounded-full text-xs font-bold capitalize"><?= esc($g['role_kode']) ?></span>
                    </td>
                    <td class="px-6 py-4 align-top" style="min-width:200px">
                        <?php if (!empty($g['mapel_list'])): ?>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach ($g['mapel_list'] as $m): ?>
                            <span class="px-2 py-0.5 bg-surface-container-low rounded text-[10px] text-on-surface-variant">
                                <?= esc($m['mapel_nama']) ?><?= $m['rombel_nama'] ? ' (' . esc($m['rombel_nama']) . ')' : '' ?>
                                <span class="text-outline/60"><?= esc($m['tahun_ajar_nama'] ?? '') ?></span>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-xs text-on-surface-variant italic">Belum ada</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 align-top text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="showEdit(<?= $g['id'] ?>)" class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="showAssign(<?= $g['id'] ?>)" class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Atur Mapel">
                                <span class="material-symbols-outlined text-sm">assignment</span>
                            </button>
                            <button onclick="confirmDelete(<?= $g['id'] ?>, <?= htmlspecialchars(json_encode($g['nama'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 hover:bg-error/10 rounded-lg text-error transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" role="dialog" aria-modal="true" aria-labelledby="modal-tambah-guru-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreate()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h4 id="modal-tambah-guru-title" class="font-headline-sm text-headline-sm text-primary mb-2">Tambah Guru</h4>
        <form id="createForm" class="space-y-4" onsubmit="return submitCreate(event)">
            <?= csrf_field() ?>
            <div>
                <label for="createNama" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Nama</label>
                <input type="text" name="nama" id="createNama" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="createEmail" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Email</label>
                <input type="email" name="email" id="createEmail" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="createPassword" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Password</label>
                <input type="password" name="password" id="createPassword" required minlength="6" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="createNip" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">NIP (opsional)</label>
                <input type="text" name="nip" id="createNip" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="createRoleId" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Role</label>
                <select name="role_id" id="createRoleId" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideCreate()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" role="dialog" aria-modal="true" aria-labelledby="modal-edit-guru-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideEdit()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h4 id="modal-edit-guru-title" class="font-headline-sm text-headline-sm text-primary mb-2">Edit Guru</h4>
        <form id="editForm" class="space-y-4" onsubmit="return submitEdit(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="editId">
            <div>
                <label for="editNama" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Nama</label>
                <input type="text" name="nama" id="editNama" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="editEmail" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Email</label>
                <input type="email" name="email" id="editEmail" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="editPassword" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Password (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="editPassword" minlength="6" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="editNip" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">NIP (opsional)</label>
                <input type="text" name="nip" id="editNip" class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="editRoleId" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Role</label>
                <select name="role_id" id="editRoleId" required class="w-full px-3 py-2.5 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideEdit()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" role="dialog" aria-modal="true" aria-labelledby="modal-assign-guru-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideAssign()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h4 id="modal-assign-guru-title" class="font-headline-sm text-headline-sm text-primary mb-2">Atur Mapel</h4>
        <p class="text-sm text-on-surface-variant mb-4" id="assignName">Guru: </p>
        <form id="assignForm" class="space-y-4" onsubmit="return submitAssign(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" id="assignUserId">
            <input type="hidden" name="assign_ta_id" id="assignTaId" value="">
            <select id="assignTaSelect" class="w-full px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Pilih Tahun Ajar</option>
                <?php foreach ($tahunAjar as $ta): ?>
                <option value="<?= $ta['id'] ?>"><?= esc($ta['tahun']) ?></option>
                <?php endforeach; ?>
            </select>
            <div id="assignMapelContainer" class="border-2 border-outline/20 rounded-xl p-3 space-y-3 max-h-64 overflow-y-auto">
                <p class="text-sm text-on-surface-variant italic">Pilih tahun ajar terlebih dahulu</p>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideAssign()">Batal</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const guruAssignments = <?= json_encode($guruAssignments) ?>;
const mapelData = <?= json_encode(array_map(fn($m) => ['id' => $m['id'], 'nama' => $m['nama']], $mapel)) ?>;
const rombelByTa = {<?php foreach ($tahunAjar as $ta): ?>
<?php
$rombelTa = array_values(array_filter($rombel, fn($r) => $r['tahun_ajar_id'] == $ta['id']));
$rombelTa = array_map(fn($r) => ['id' => $r['id'], 'nama' => $r['nama']], $rombelTa);
?><?= $ta['id'] ?>: <?= json_encode($rombelTa) ?>,
<?php endforeach; ?>};

// --- Filter ---
function filterGuru() {
    const role = document.getElementById('filterRole')?.value || '';
    const q = document.getElementById('searchNama')?.value.toLowerCase() || '';
    document.querySelectorAll('.guru-row').forEach(row => {
        const matchRole = !role || row.dataset.role === role;
        const matchNama = row.textContent.toLowerCase().includes(q);
        row.style.display = matchRole && matchNama ? '' : 'none';
    });
}
document.getElementById('filterRole')?.addEventListener('change', filterGuru);
document.getElementById('searchNama')?.addEventListener('keyup', filterGuru);

// --- Create ---
function showCreate() {
    document.getElementById('createForm').reset();
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createModal').classList.add('flex');
}
function hideCreate() { toggleModal('createModal', false); }

function submitCreate(e) {
    e.preventDefault();
    const form = document.getElementById('createForm');
    const data = new FormData(form);

    function offlineSave() {
        const obj = {};
        data.forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
        savePendingAdmin('POST', '<?= base_url('admin/guru/store') ?>', obj).then(function() {
            safeNotify('Success', 'Data guru tersimpan lokal.'); hideCreate();
        });
        safeUnblock('#createModal .glass-card');
    }

    if (!navigator.onLine) { offlineSave(); return false; }

    safeBlock('#createModal .glass-card');
    fetch('<?= base_url('admin/guru/store') ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message || 'Guru berhasil ditambahkan'); hideCreate(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal menyimpan'); }
        })
        .catch(function() { offlineSave(); })
        .finally(() => safeUnblock('#createModal .glass-card'));
    return false;
}

// --- Edit ---
function showEdit(id) {
    document.getElementById('editId').value = id;
    safeBlock('#guruTableCard');
    fetch('<?= base_url('admin/guru/get') ?>/' + id)
        .then(r => r.json())
        .then(d => {
            document.getElementById('editNama').value = d.nama || '';
            document.getElementById('editEmail').value = d.email || '';
            document.getElementById('editNip').value = d.nip || '';
            document.getElementById('editRoleId').value = d.role_id || '';
            toggleModal('editModal', true);
        })
        .catch(() => safeNotify('Failure', 'Gagal memuat data guru'))
        .finally(() => safeUnblock('#guruTableCard'));
}
function hideEdit() { toggleModal('editModal', false); }

function submitEdit(e) {
    e.preventDefault();
    const id = document.getElementById('editId').value;
    const form = document.getElementById('editForm');
    const data = new FormData(form);

    function offlineSave() {
        const obj = {};
        data.forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
        savePendingAdmin('POST', '<?= base_url('admin/guru/update') ?>/' + id, obj).then(function() {
            safeNotify('Success', 'Data guru tersimpan lokal.'); hideEdit();
        });
        safeUnblock('#editModal .glass-card');
    }

    if (!navigator.onLine) { offlineSave(); return false; }

    safeBlock('#editModal .glass-card');
    fetch('<?= base_url('admin/guru/update') ?>/' + id, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message || 'Guru berhasil diupdate'); hideEdit(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal mengupdate'); }
        })
        .catch(function() { offlineSave(); })
        .finally(() => safeUnblock('#editModal .glass-card'));
    return false;
}

// --- Assign ---
function rebuildAssignCheckboxes(taId, userId) {
    const container = document.getElementById('assignMapelContainer');
    container.innerHTML = '';
    if (!taId) {
        container.innerHTML = '<p class="text-sm text-on-surface-variant italic">Pilih tahun ajar terlebih dahulu</p>';
        return;
    }
    const rombels = rombelByTa[taId] || [];
    if (!rombels.length) {
        container.innerHTML = '<p class="text-sm text-on-surface-variant italic">Tidak ada kelas di tahun ajar ini</p>';
        return;
    }
    mapelData.forEach(m => {
        const section = document.createElement('div');
        const header = document.createElement('div');
        header.className = 'text-sm font-bold text-on-surface mb-1.5 flex items-center gap-2';
        header.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>' + m.nama;
        section.appendChild(header);
        const grid = document.createElement('div');
        grid.className = 'ml-4 grid grid-cols-2 gap-1.5';
        rombels.forEach(r => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 cursor-pointer hover:bg-surface-container-low rounded-lg p-1.5 transition-colors';
            const cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.name = 'assign[' + taId + '][' + m.id + '][]';
            cb.value = r.id;
            cb.dataset.taId = taId;
            cb.dataset.mapelId = m.id;
            cb.dataset.rombelId = r.id;
            cb.className = 'w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30 assign-cb';
            const span = document.createElement('span');
            span.className = 'text-xs text-on-surface-variant';
            span.textContent = r.nama;
            label.appendChild(cb);
            label.appendChild(span);
            grid.appendChild(label);
        });
        section.appendChild(grid);
        container.appendChild(section);
    });
    // Restore existing checks
    if (userId && guruAssignments[userId]?.[taId]) {
        Object.entries(guruAssignments[userId][taId]).forEach(([mapelId, rombelIds]) => {
            rombelIds.forEach(rombelId => {
                const cb = container.querySelector('input[data-ta-id="' + taId + '"][data-mapel-id="' + mapelId + '"][data-rombel-id="' + rombelId + '"]');
                if (cb) cb.checked = true;
            });
        });
    }
}

function showAssign(id) {
    document.getElementById('assignUserId').value = id;
    const row = document.querySelector('.guru-row[data-id="' + id + '"]');
    const nama = row ? row.querySelector('td').textContent.trim() : '';
    document.getElementById('assignName').textContent = 'Guru: ' + nama;
    const sel = document.getElementById('assignTaSelect');
    sel.value = '';
    document.getElementById('assignTaId').value = '';
    rebuildAssignCheckboxes('', id);
    toggleModal('assignModal', true);
}
function hideAssign() { toggleModal('assignModal', false); }

document.getElementById('assignTaSelect')?.addEventListener('change', function() {
    document.getElementById('assignTaId').value = this.value;
    rebuildAssignCheckboxes(this.value, document.getElementById('assignUserId').value);
});

function submitAssign(e) {
    e.preventDefault();
    const id = document.getElementById('assignUserId').value;
    const form = document.getElementById('assignForm');
    const data = new FormData(form);

    function offlineSave() {
        const obj = {};
        data.forEach(function(v,k) { if (k !== 'csrf_test_name') obj[k]=v; });
        savePendingAdmin('POST', '<?= base_url('admin/guru/assign') ?>/' + id, obj).then(function() {
            safeNotify('Success', 'Penugasan tersimpan lokal.'); hideAssign();
        });
        safeUnblock('#assignModal .glass-card');
    }

    if (!navigator.onLine) { offlineSave(); return false; }

    safeBlock('#assignModal .glass-card');
    fetch('<?= base_url('admin/guru/assign') ?>/' + id, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message || 'Penugasan berhasil disimpan'); hideAssign(); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal menyimpan penugasan'); }
        })
        .catch(function() { offlineSave(); })
        .finally(() => safeUnblock('#assignModal .glass-card'));
    return false;
}

// --- Delete ---
function confirmDelete(id, nama) {
    safeConfirm(
        'Hapus Guru?',
        'Yakin ingin menghapus <b>' + nama + '</b>? Semua data terkait akan dihapus.',
        'Ya, Hapus',
        'Batal',
        function() {
            if (!navigator.onLine) {
                savePendingAdmin('POST', '<?= base_url('admin/guru/delete') ?>/' + id, { id: id }).then(function() {
                    safeNotify('Success', 'Permintaan hapus tersimpan lokal.');
                });
                return;
            }
            safeBlock('#guruTableCard');
            fetch('<?= base_url('admin/guru/delete') ?>/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        safeNotify('Success', res.message || 'Guru berhasil dihapus');
                        const row = document.querySelector('.guru-row[data-id="' + id + '"]');
                        if (row) row.remove();
                    } else {
                        safeNotify('Failure', res.message || 'Gagal menghapus');
                    }
                })
                .catch(() => savePendingAdmin('POST', '<?= base_url('admin/guru/delete') ?>/' + id, { id: id }).then(function() {
                    safeNotify('Success', 'Permintaan hapus tersimpan lokal.');
                }))
                .finally(() => safeUnblock('#guruTableCard'));
        }
    );
}

// --- Helpers ---
function toggleModal(id, show) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('hidden', !show);
    el.classList.toggle('flex', show);
}
</script>
<?= $this->endSection() ?>
