<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Data Santri<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Data Santri'],
    ]
]) ?>

<div class="flex items-center justify-between">
    <div>
        <h3 class="font-headline-sm text-headline-sm text-primary">Data Santri</h3>
        <p class="text-on-surface-variant text-sm">Kelola data santri pondok pesantren</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="document.getElementById('excelInput').click()"
                class="flex items-center gap-2 px-4 py-2 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm hover:bg-surface-variant transition-all">
            <span class="material-symbols-outlined text-sm">table_chart</span>
            Import Excel
        </button>
        <input type="file" id="excelInput" accept=".xlsx,.xls" class="hidden"
               onchange="importExcel(this)">
        <button onclick="showCreate()"
                class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-primary/20 transition-all">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Santri
        </button>
    </div>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5" id="santriTableCard">
    <div class="p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-4 border-b border-outline-variant/20">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-full md:w-64">
                <span class="material-symbols-outlined text-sm absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20" placeholder="Cari nama/NIS..." type="text" id="searchInput" aria-label="Cari santri"/>
            </div>
            <select id="filterJK" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua JK</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
            <select id="filterStatus" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
            <select id="filterRombel" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                <option value="">Semua Rombel</option>
                <option value="0">Belum Punya Rombel</option>
                <?php foreach ($rombel as $r): ?>
                <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <span class="text-sm text-on-surface-variant">Total: <?= count($santri) ?> santri</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-surface-container-low/50">
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">NIS</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Nama</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">JK</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Tempat Lahir</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Tanggal Lahir</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Rombel</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase">Wali</th>
                    <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10" id="santriTableBody">
                <?php if (!empty($santri)): foreach ($santri as $s): ?>
                <tr class="santri-row hover:bg-white/40 transition-colors"
                    data-id="<?= $s['id'] ?>"
                    data-jk="<?= $s['jenkel'] ?? '' ?>"
                    data-aktif="<?= $s['is_active'] ?? 1 ?>"
                    data-rombel="<?= $s['rombel_id'] ?? '' ?>">
                    <td class="px-6 py-4 text-sm font-bold text-on-surface"><?= esc($s['nis']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs"><?= esc(strtoupper(substr($s['nama'], 0, 1))) ?></div>
                            <span class="font-bold text-on-surface"><?= esc($s['nama']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="text-xs font-bold <?= $s['jenkel'] == 'L' ? 'text-primary' : 'text-pink-600' ?>"><?= $s['jenkel'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></span></td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= esc($s['tempat_lahir'] ?? '—') ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= esc($s['tanggal_lahir'] ? date('d/m/Y', strtotime($s['tanggal_lahir'])) : '—') ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= esc($s['rombel_nama'] ?? '—') ?></td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant"><?= esc($s['nama_wali'] ?? '—') ?></td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="showEdit(<?= $s['id'] ?>)" class="p-2 min-h-[44px] min-w-[44px] hover:bg-primary/10 rounded-lg text-primary transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-sm">edit</span>
                            </button>
                            <button onclick="confirmDelete(<?= $s['id'] ?>, <?= htmlspecialchars(json_encode($s['nama'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>)" class="p-2 min-h-[44px] min-w-[44px] hover:bg-error/10 rounded-lg text-error transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="8" class="px-6 py-12 text-center text-on-surface-variant text-sm">Belum ada data santri</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="santriModal" role="dialog" aria-modal="true" aria-labelledby="santriModalTitle" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideSantriModal()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <h4 class="font-headline-sm text-headline-sm text-primary mb-2" id="santriModalTitle">Tambah Santri</h4>
        <form id="santriForm" class="space-y-5" onsubmit="return submitSantri(event)">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="santriId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="fieldNis" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">NIS</label>
                    <input type="text" name="nis" id="fieldNis" required
                           class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="Contoh: 2409001"/>
                </div>
                <div>
                    <label for="fieldNama" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Nama Lengkap</label>
                    <input type="text" name="nama" id="fieldNama" required
                           class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                           placeholder="Nama santri"/>
                </div>
                <div>
                    <label for="fieldJenkel" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label>
                    <select name="jenkel" id="fieldJenkel" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="fieldTempatLahir" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="fieldTempatLahir"
                           class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                </div>
                <div>
                    <label for="fieldTanggalLahir" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="fieldTanggalLahir"
                           class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                </div>
                <div>
                    <label for="fieldNamaWali" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Nama Wali</label>
                    <input type="text" name="nama_wali" id="fieldNamaWali"
                           class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
                </div>
                <div>
                    <label for="fieldRombel" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Rombel</label>
                    <select name="rombel_id" id="fieldRombel" class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        <option value="">— Pilih Rombel —</option>
                        <?php foreach ($rombel as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= esc($r['nama']) ?> (<?= esc($r['tahun_ajar_nama'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label for="fieldAlamat" class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider mb-1.5 block">Alamat</label>
                <textarea name="alamat" id="fieldAlamat" rows="3"
                          class="w-full px-4 py-3 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 outline-none transition-all"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideSantriModal()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation uses native confirm() -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// --- Filter ---
function filterSantri() {
    const q = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const jk = document.getElementById('filterJK')?.value || '';
    const aktif = document.getElementById('filterStatus')?.value || '';
    const rombelId = document.getElementById('filterRombel')?.value || '';
    document.querySelectorAll('.santri-row').forEach(row => {
        const text = row.textContent.toLowerCase();
        const matchSearch = !q || text.includes(q);
        const matchJK = !jk || row.dataset.jk === jk;
        const matchAktif = !aktif || row.dataset.aktif === aktif;
        const matchRombel = !rombelId || (rombelId === '0' ? !row.dataset.rombel : row.dataset.rombel === rombelId);
        row.style.display = matchSearch && matchJK && matchAktif && matchRombel ? '' : 'none';
    });
}
['searchInput', 'filterJK', 'filterStatus', 'filterRombel'].forEach(id => {
    document.getElementById(id)?.addEventListener('change', filterSantri);
    if (id === 'searchInput') document.getElementById(id)?.addEventListener('keyup', filterSantri);
});

// --- Modal ---
function showCreate() {
    document.getElementById('santriModalTitle').textContent = 'Tambah Santri Baru';
    document.getElementById('santriForm').reset();
    document.getElementById('santriId').value = '';
    document.getElementById('santriModal').classList.remove('hidden');
    document.getElementById('santriModal').classList.add('flex');
}

function showEdit(id) {
    document.getElementById('santriModalTitle').textContent = 'Edit Santri';
    document.getElementById('santriId').value = id;
    safeBlock('#santriTableCard');
    fetch('<?= base_url('admin/santri/get') ?>/' + id)
        .then(r => r.json())
        .then(d => {
            document.getElementById('fieldNis').value = d.nis || '';
            document.getElementById('fieldNama').value = d.nama || '';
            document.getElementById('fieldJenkel').value = d.jenkel || 'L';
            document.getElementById('fieldTempatLahir').value = d.tempat_lahir || '';
            document.getElementById('fieldTanggalLahir').value = d.tanggal_lahir || '';
            document.getElementById('fieldNamaWali').value = d.nama_wali || '';
            document.getElementById('fieldAlamat').value = d.alamat || '';
            document.getElementById('fieldRombel').value = d.rombel_id || '';
            document.getElementById('santriModal').classList.remove('hidden');
            document.getElementById('santriModal').classList.add('flex');
        })
        .catch(() => safeNotify('Failure', 'Gagal memuat data santri'))
        .finally(() => safeUnblock('#santriTableCard'));
}

function hideSantriModal() {
    document.getElementById('santriModal').classList.add('hidden');
    document.getElementById('santriModal').classList.remove('flex');
}

// --- Submit ---
function submitSantri(e) {
    e.preventDefault();
    const id = document.getElementById('santriId').value;
    const isEdit = !!id;
    const url = isEdit
        ? '<?= base_url('admin/santri/update') ?>/' + id
        : '<?= base_url('admin/santri/store') ?>';
    const form = document.getElementById('santriForm');
    const data = new FormData(form);

    function handleOffline() {
        const obj = {};
        data.forEach(function(v, k) { if (k !== 'csrf_test_name') obj[k] = v; });
        savePendingAdmin('POST', url, obj).then(function() {
            safeNotify('Success', 'Data santri tersimpan lokal. Akan dikirim saat online.');
            hideSantriModal();
        });
        safeUnblock('#santriModal .glass-card');
    }

    if (!navigator.onLine) { handleOffline(); return false; }

    safeBlock('#santriModal .glass-card');
    fetch(url, { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                safeNotify('Success', res.message || 'Santri berhasil disimpan');
                hideSantriModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                safeNotify('Failure', res.message || 'Gagal menyimpan');
            }
        })
        .catch(function() { handleOffline(); })
        .finally(() => safeUnblock('#santriModal .glass-card'));
    return false;
}

// --- Delete ---
function confirmDelete(id, nama) {
    safeConfirm(
        'Hapus Santri?',
        'Yakin ingin menghapus <b>' + nama + '</b>? Data tidak dapat dikembalikan.',
        'Ya, Hapus',
        'Batal',
        function() {
            if (!navigator.onLine) {
                savePendingAdmin('POST', '<?= base_url('admin/santri/delete') ?>/' + id, { id: id }).then(function() {
                    safeNotify('Success', 'Permintaan hapus tersimpan lokal. Akan diproses saat online.');
                });
                return;
            }
            safeBlock('#santriTableCard');
            fetch('<?= base_url('admin/santri/delete') ?>/' + id, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        safeNotify('Success', res.message || 'Santri berhasil dihapus');
                        const row = document.querySelector('.santri-row[data-id="' + id + '"]');
                        if (row) row.remove();
                    } else {
                        safeNotify('Failure', res.message || 'Gagal menghapus');
                    }
                })
                .catch(() => savePendingAdmin('POST', '<?= base_url('admin/santri/delete') ?>/' + id, { id: id }).then(function() {
                    safeNotify('Success', 'Permintaan hapus tersimpan lokal.');
                }))
                .finally(() => safeUnblock('#santriTableCard'));
        }
    );
}

// --- Import Excel ---
function importExcel(input) {
    const file = input.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('file', file);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    const rombelFilter = document.getElementById('filterRombel')?.value;
    if (rombelFilter && rombelFilter !== '0') {
        formData.append('rombel_id', rombelFilter);
    }
    safeBlock('#santriTableCard');
    fetch('<?= base_url('admin/santri/import-excel') ?>', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                safeNotify('Success', res.message || 'Import berhasil');
                if (res.duplicates && res.duplicates.length) {
                    safeNotify('Warning', 'NIS duplikat dilewati: ' + res.duplicates.join(', '));
                }
                setTimeout(() => location.reload(), 1000);
            } else {
                safeNotify('Failure', res.message || 'Gagal import');
            }
        })
        .catch(() => safeNotify('Failure', 'Terjadi kesalahan server'))
        .finally(() => {
            safeUnblock('#santriTableCard');
            input.value = '';
        });
}
</script>
<?= $this->endSection() ?>
