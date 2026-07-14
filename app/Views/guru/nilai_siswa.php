<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Nilai - <?= esc($siswa['nama'] ?? '') ?><?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_guru') ?><?= $this->endSection() ?>

<?php $firstKategoriId = $activeKategoriId ?: (!empty($kategoriList) ? $kategoriList[0]['id'] : 0); ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('guru')], ['label' => 'Kelas Saya', 'url' => base_url('guru/input-saya')], ['label' => ($mapel['nama'] ?? '') . ' — ' . ($rombel['nama'] ?? ''), 'url' => base_url('guru/nilai/mapel/' . $mapel['id'] . '/kelas/' . $rombel['id'])], ['label' => ($siswa['nama'] ?? '')]]]) ?>
<div class="sticky top-0 z-10 -mx-6 -mt-6 px-6 pt-6 pb-4 bg-surface/90 backdrop-blur-xl border-b border-outline-variant/10">
    <a href="<?= base_url('guru/nilai/mapel/' . $mapel['id'] . '/kelas/' . $rombel['id']) ?>" class="inline-flex items-center gap-1 text-primary text-sm mb-3 transition-all">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>

    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-lg ring-2 ring-primary/20 shrink-0">
            <?= esc(strtoupper(substr($siswa['nama'] ?? '?', 0, 1))) ?>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-headline-sm text-headline-sm text-primary truncate"><?= esc($siswa['nama'] ?? '—') ?></h3>
            <p class="text-xs text-on-surface-variant truncate">
                NIS: <?= esc($siswa['nis'] ?? '—') ?> •
                <?= esc($mapel['nama'] ?? '—') ?> •
                <?= esc($rombel['nama'] ?? '—') ?> •
                KKM: <?= $mapel['kkm'] ?? '65' ?>
            </p>
            <div id="globalSyncStatus" class="text-xs text-green-600 flex items-center gap-1.5 mt-1">
                <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                <span>Tersinkronisasi</span>
            </div>
        </div>


    </div>

    <!-- Tab Kategori -->
    <?php if (!empty($kategoriList)): ?>
    <div class="flex border-b border-outline-variant/20 mt-4 -mb-4 overflow-x-auto scrollbar-hide">
        <?php foreach ($kategoriList as $j): ?>
        <button onclick="switchTab(<?= $j['id'] ?>)"
                id="tab-kategori-<?= $j['id'] ?>"
                role="tab"
                aria-selected="<?= $j['id'] == $activeKategoriId ? 'true' : 'false' ?>"
                aria-controls="panel-kategori-<?= $j['id'] ?>"
                class="tab-kategori-btn px-5 py-3 text-sm font-bold whitespace-nowrap transition-all border-b-2
                       <?= $j['id'] == $activeKategoriId ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary hover:bg-primary/5' ?>">
            <?= esc($displayNameByKategoriId[$j['id']] ?? $j['nama']) ?>
        </button>
        <?php endforeach; ?>
        <button onclick="switchTab('akhir')"
                id="tab-kategori-akhir"
                role="tab"
                aria-selected="<?= $activeKategoriId === 'akhir' ? 'true' : 'false' ?>"
                aria-controls="panel-kategori-akhir"
                class="tab-kategori-btn px-5 py-3 text-sm font-bold whitespace-nowrap transition-all border-b-2
                       <?= $activeKategoriId === 'akhir' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary hover:bg-primary/5' ?>">
            <span class="material-symbols-outlined text-sm align-middle mr-1">grade</span>
            Nilai Akhir
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Konten tab kategori -->
<?php if (!empty($kategoriList)): ?>
<div class="mt-6">
    <?php foreach ($kategoriList as $i => $j): ?>
    <div id="panel-kategori-<?= $j['id'] ?>" role="tabpanel" aria-labelledby="tab-kategori-<?= $j['id'] ?>" class="tab-kategori-content <?= $j['id'] != $activeKategoriId ? 'hidden' : '' ?>">
        <?php if ($j['id'] == $activeKategoriId && $firstTab): ?>
            <?= view('guru/nilai_siswa_form', [
                'mapel'        => $mapel,
                'rombel'       => $rombel,
                'siswa'        => $siswa,
                'kategori'        => $firstTab['kategori'],
                'displayName'  => $displayNameByKategoriId[$firstTab['kategori']['id']] ?? $firstTab['kategori']['nama'],
                'kriteria'     => $firstTab['kriteria'],
                'kriteriaData' => $firstTab['kriteriaData'],
                'progres'      => $firstTab['progres'],
            ]) ?>
        <?php else: ?>
        <div class="tab-loading flex items-center justify-center py-20 text-on-surface-variant">
            <span class="material-symbols-outlined animate-spin mr-2">progress_activity</span>
            Memuat...
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <!-- Tab: Nilai Akhir -->
    <div id="panel-kategori-akhir" role="tabpanel" aria-labelledby="tab-kategori-akhir" class="tab-kategori-content <?= $activeKategoriId !== 'akhir' ? 'hidden' : '' ?>">
        <?= view('guru/nilai_siswa_akhir', [
            'mapel'     => $mapel,
            'rombel'    => $rombel,
            'siswa'     => $siswa,
            'autoGrade' => $autoGrade,
            'nilaiAkhir'=> $nilaiAkhir,
            'recapData' => $recapData,
            'kategoriList' => $kategoriList,
            'displayNameByKategoriId' => $displayNameByKategoriId,
            'isTasmi'   => $isTasmi,
        ]) ?>
    </div>
</div>
<?php else: ?>
<div class="mt-6 text-center py-20 text-on-surface-variant">
    <span class="material-symbols-outlined text-4xl mb-3 block">book_4</span>
    <p class="text-sm">Belum ada kategori untuk mapel ini</p>
</div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const BASE_URL = <?= json_encode(base_url()) ?>;
const loadedTabs = new Set();
const autosaveTimers = {};
<?php if (!empty($kategoriList) && $activeKategoriId && $activeKategoriId !== 'akhir'): ?>
loadedTabs.add(<?= $activeKategoriId ?>);
<?php endif; ?>

function switchTab(tabId) {
    document.querySelectorAll('.tab-kategori-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-kategori-btn').forEach(el => {
        el.classList.remove('text-primary', 'border-primary');
        el.classList.add('text-on-surface-variant', 'border-transparent');
    });

    const content = document.getElementById('panel-kategori-' + tabId);
    const btn = document.getElementById('tab-kategori-' + tabId);
    if (content) content.classList.remove('hidden');
    if (btn) {
        btn.classList.remove('text-on-surface-variant', 'border-transparent');
        btn.classList.add('text-primary', 'border-primary');
    }

    const url = new URL(window.location);
    url.searchParams.set('kategori', tabId);
    window.history.replaceState({}, '', url);

    if (!loadedTabs.has(tabId) && tabId !== 'akhir') {
        loadedTabs.add(tabId);
        const loadingDiv = content?.querySelector('.tab-loading');
        if (loadingDiv) {
            loadingDiv.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2">progress_activity</span> Memuat...';
        }

        fetch('<?= base_url('guru/nilai/siswa/form') ?>/' + <?= $siswa['id'] ?> + '/mapel/' + <?= $mapel['id'] ?> + '/kelas/' + <?= $rombel['id'] ?> + '/kategori/' + tabId)
            .then(res => res.text())
            .then(html => {
                if (content) {
                    content.innerHTML = html;
                    initGradeForm(parseInt(tabId));
                }
            })
            .catch(() => {
                if (content) {
                    content.innerHTML = '<div class="text-center py-20 text-status-offline">Gagal memuat data</div>';
                }
            });
    }
}

function updateProgress(kategoriId) {
    const container = document.getElementById('panel-kategori-' + kategoriId);
    if (!container) return;
    const form = container.querySelector('form');
    if (!form) return;
    const rows = form.querySelectorAll('tbody tr');
    if (rows.length === 0) return;
    let filled = 0;
    rows.forEach(function(row) {
        const num = row.querySelector('.kriteria-input');
        const cb = row.querySelector('.kriteria-checkbox');
        const txt = row.querySelector('input[type="text"][name*="[huruf]"]');
        if (num && num.value !== '') filled++;
        else if (cb && cb.checked) filled++;
        else if (txt && txt.value.trim() !== '') filled++;
    });
    const total = rows.length;
    const bar = form.querySelector('[id^="progress-"]');
    const label = form.querySelector('[class*="text-xs"][class*="font-bold"]:not([class*="text-outline"])');
    if (bar) {
        const pct = total > 0 ? Math.round((filled / total) * 100) : 0;
        bar.style.width = pct + '%';
        bar.style.background = pct === 100 ? '#10b981' : '#6366f1';
    }
    if (label && label.textContent.includes('/')) {
        label.textContent = filled + '/' + total;
        label.className = 'text-xs font-bold ' + (filled === total ? 'text-emerald-600' : 'text-primary');
    }
}

function initGradeForm(kategoriId) {
    const container = document.getElementById('panel-kategori-' + kategoriId);
    if (!container) return;

    container.querySelectorAll('.kriteria-input').forEach(input => {
        input.addEventListener('input', function() { calculateTotal(kategoriId); updateProgress(kategoriId); });
    });
    container.querySelectorAll('.kriteria-checkbox').forEach(input => {
        input.addEventListener('change', function() { calculateCheckboxTotal(kategoriId); updateProgress(kategoriId); });
    });
    container.querySelectorAll('input[type="text"][name*="[huruf]"]').forEach(input => {
        input.addEventListener('input', function() { updateProgress(kategoriId); });
    });

    const manualInput = container.querySelector('#nilaiTotal-' + kategoriId);
    if (manualInput && manualInput.classList.contains('kriteria-input') === false) {
        manualInput.addEventListener('input', function() {
            updatePredikat(parseFloat(this.value) || 0, kategoriId);
        });
    }

    calculateTotal(kategoriId);
    calculateCheckboxTotal(kategoriId);
    updateProgress(kategoriId);
}

function calculateCheckboxTotal(kategoriId) {
    const container = document.getElementById('panel-kategori-' + kategoriId);
    if (!container) return;
    const checkboxes = container.querySelectorAll('.kriteria-checkbox');
    if (checkboxes.length === 0) return;
    const checked = container.querySelectorAll('.kriteria-checkbox:checked').length;
    const total = checkboxes.length;
    const pct = total > 0 ? Math.round((checked / total) * 100) : 0;
    const totalEl = document.getElementById('nilaiTotal-' + kategoriId);
    if (totalEl) { totalEl.value = pct; updatePredikat(pct, kategoriId); }
}

function calculateTotal(kategoriId) {
    const container = document.getElementById('panel-kategori-' + kategoriId);
    if (!container) return;
    const form = container.querySelector('form');
    const hitungKosong = parseInt(form?.dataset.hitungKosong) || 0;
    let totalBobot = 0, totalNilai = 0;
    container.querySelectorAll('.kriteria-input').forEach(input => {
        const bobot = parseFloat(input.dataset.bobot) || 0;
        const skala = parseFloat(input.dataset.skala) || 100;
        if (input.value.trim() === '' && !hitungKosong) return;
        const nilai = parseFloat(input.value) || 0;
        totalBobot += bobot;
        totalNilai += (nilai / skala) * bobot;
    });
    if (totalBobot > 0) {
        const rata = Math.round((totalNilai / totalBobot) * 100);
        const totalEl = document.getElementById('nilaiTotal-' + kategoriId);
        if (totalEl) { totalEl.value = rata; updatePredikat(rata, kategoriId); }
    }
}

function updatePredikat(nilai, kategoriId) {
    const el = document.getElementById('predikatDisplay-' + kategoriId);
    const hidden = document.getElementById('predikatHidden-' + kategoriId);
    if (!el) return;
    let predikat, label;
    if (nilai >= 85) { predikat = 'A'; label = 'A (Mumtaz)'; }
    else if (nilai >= 70) { predikat = 'B'; label = 'B (Jayyid)'; }
    else if (nilai >= 55) { predikat = 'C'; label = 'C (Maqbul)'; }
    else if (nilai >= 40) { predikat = 'D'; label = 'D (Naqis)'; }
    else if (nilai > 0) { predikat = 'E'; label = 'E (Dhaif)'; }
    else { predikat = ''; label = '—'; }
    el.textContent = label;
    if (hidden) hidden.value = predikat;
    el.className = nilai > 0
        ? 'px-4 py-2 rounded-lg text-sm font-bold bg-primary-container/20 text-primary transition-all duration-200'
        : 'px-4 py-2 rounded-lg text-sm font-bold bg-surface-container-low text-on-surface-variant transition-all duration-200';
}

// --- Autosave: IDB-first pattern ---
function debounceRowSave(row, kategoriId) {
    const key = kategoriId + '_' + (row.dataset.kriteriaId || Math.random());
    clearTimeout(autosaveTimers[key]);
    autosaveTimers[key] = setTimeout(function() { saveRow(row, kategoriId); }, 1500);
}

function saveRow(row, kategoriId) {
    const form = row.closest('form');
    if (!form) return;

    const kriteriaInput = row.querySelector('input[name$="[nilai]"]');
    const checkboxInput = row.querySelector('input[name$="[selesai]"]');
    const catatanInput = row.querySelector('input[name$="[catatan]"]');
    const nameSource = kriteriaInput || checkboxInput || catatanInput;
    if (!nameSource) return;

    const match = nameSource.name.match(/kriteria\[(\d+)\]/);
    if (!match) return;

    var payload = {
        siswa_id: form.querySelector('[name="siswa_id"]')?.value || '',
        mapel_id: form.querySelector('[name="mapel_id"]')?.value || '',
        rombel_id: form.querySelector('[name="rombel_id"]')?.value || '',
        kategori_id: kategoriId,
        kriteria_id: match[1],
        nilai: kriteriaInput ? kriteriaInput.value : '',
        selesai: checkboxInput && checkboxInput.checked ? '1' : '',
        catatan: catatanInput ? catatanInput.value : '',
    };

    if (navigator.onLine) {
        // Online: kirim ke server langsung
        const fd = new FormData(form);
        // Override dengan nilai terbaru dari row ini
        if (kriteriaInput) fd.set('kriteria[' + match[1] + '][nilai]', payload.nilai);
        if (checkboxInput) fd.set('kriteria[' + match[1] + '][selesai]', payload.selesai);
        if (catatanInput) fd.set('kriteria[' + match[1] + '][catatan]', payload.catatan);
        calculateTotal(kategoriId);
        calculateCheckboxTotal(kategoriId);
        const totalEl = document.getElementById('nilaiTotal-' + kategoriId);
        const predikatHidden = document.getElementById('predikatHidden-' + kategoriId);
        if (totalEl && totalEl.value) fd.set('nilai', totalEl.value);
        if (predikatHidden && predikatHidden.value) fd.set('predikat', predikatHidden.value);

        fetch('<?= base_url('guru/nilai/save') ?>', {
            method: 'POST',
            body: fd,
            headers: { 'X-Offline-Sync': '1' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateGlobalSyncStatus('synced');
            } else {
                updateGlobalSyncStatus('pending', data.message || 'Gagal menyimpan. Data disimpan lokal.');
                // Fallback: simpan ke IDB
                savePendingKriteria(payload.siswa_id, payload.mapel_id, payload.rombel_id, payload.kategori_id, payload.kriteria_id, payload.nilai, payload.selesai, payload.catatan);
            }
        })
        .catch(function() {
            // Fallback ke IDB jika server error
            savePendingKriteria(payload.siswa_id, payload.mapel_id, payload.rombel_id, payload.kategori_id, payload.kriteria_id, payload.nilai, payload.selesai, payload.catatan)
                .then(function() {
                    updateGlobalSyncStatus('pending', 'Koneksi terputus. Data tersimpan lokal.');
                });
        });
    } else {
        // Offline: simpan ke IDB
        savePendingKriteria(payload.siswa_id, payload.mapel_id, payload.rombel_id, payload.kategori_id, payload.kriteria_id, payload.nilai, payload.selesai, payload.catatan)
            .then(function() {
                updateGlobalSyncStatus('pending', 'Data menunggu sinkronisasi...');
                if (window.triggerSync) window.triggerSync();
            });
    }
}

function simpanFormNilai(kategoriId) {
    const container = document.getElementById('panel-kategori-' + kategoriId);
    if (!container) return;
    const form = container.querySelector('form');
    if (!form) return;

    const btn = document.getElementById('btnSimpanNilai-' + kategoriId);
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Menyimpan...';

    function finish(success, msg) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        const el = document.getElementById('globalSyncStatus');
        if (el) {
            if (success) {
                el.innerHTML = '<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> ' + (msg || 'Tersimpan');
                el.className = 'text-xs text-green-600 flex items-center gap-1.5 mt-1';
            } else {
                el.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> ' + (msg || 'Gagal menyimpan');
                el.className = 'text-xs text-red-600 flex items-center gap-1.5 mt-1';
            }
        }
    }

    if (!navigator.onLine) {
        // Simpan ke IDB jika offline
        const rows = container.querySelectorAll('tr');
        let promises = [];
        rows.forEach(row => {
            const kriteriaInput = row.querySelector('input[name$="[nilai]"]');
            const checkboxInput = row.querySelector('input[name$="[selesai]"]');
            const catatanInput = row.querySelector('input[name$="[catatan]"]');
            const nameSource = kriteriaInput || checkboxInput || catatanInput;
            if (!nameSource) return;
            const match = nameSource.name.match(/kriteria\[(\d+)\]/);
            if (!match) return;
            const payload = {
                siswa_id: form.querySelector('[name="siswa_id"]')?.value || '',
                mapel_id: form.querySelector('[name="mapel_id"]')?.value || '',
                rombel_id: form.querySelector('[name="rombel_id"]')?.value || '',
                kategori_id: kategoriId,
                kriteria_id: match[1],
                nilai: kriteriaInput ? kriteriaInput.value : '',
                selesai: checkboxInput && checkboxInput.checked ? '1' : '',
                catatan: catatanInput ? catatanInput.value : '',
            };
            promises.push(savePendingKriteria(payload.siswa_id, payload.mapel_id, payload.rombel_id, payload.kategori_id, payload.kriteria_id, payload.nilai, payload.selesai, payload.catatan));
        });
        Promise.all(promises).then(function() {
            finish(true, 'Data tersimpan lokal. Sinkronisasi otomatis saat online.');
            updateGlobalSyncStatus('pending', 'Data menunggu sinkronisasi...');
        }).catch(function() {
            finish(false, 'Gagal menyimpan data lokal.');
        });
        return;
    }

    // Online: kirim ke server via fetch
    const formData = new FormData(form);
    calculateTotal(kategoriId);
    calculateCheckboxTotal(kategoriId);
    // Baca nilai total & predikat yang sudah dihitung
    const totalEl = document.getElementById('nilaiTotal-' + kategoriId);
    const predikatHidden = document.getElementById('predikatHidden-' + kategoriId);
    if (totalEl && totalEl.value) formData.set('nilai', totalEl.value);
    if (predikatHidden && predikatHidden.value) formData.set('predikat', predikatHidden.value);

    fetch('<?= base_url('guru/nilai/save') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Offline-Sync': '1' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            finish(true, 'Nilai berhasil disimpan.');
        } else {
            finish(false, data.message || 'Gagal menyimpan nilai.');
        }
    })
    .catch(function() {
        // Fallback ke IDB jika server error
        const rows = container.querySelectorAll('tr');
        let promises = [];
        rows.forEach(row => {
            const kriteriaInput = row.querySelector('input[name$="[nilai]"]');
            const checkboxInput = row.querySelector('input[name$="[selesai]"]');
            const catatanInput = row.querySelector('input[name$="[catatan]"]');
            const nameSource = kriteriaInput || checkboxInput || catatanInput;
            if (!nameSource) return;
            const match = nameSource.name.match(/kriteria\[(\d+)\]/);
            if (!match) return;
            const payload = {
                siswa_id: form.querySelector('[name="siswa_id"]')?.value || '',
                mapel_id: form.querySelector('[name="mapel_id"]')?.value || '',
                rombel_id: form.querySelector('[name="rombel_id"]')?.value || '',
                kategori_id: kategoriId,
                kriteria_id: match[1],
                nilai: kriteriaInput ? kriteriaInput.value : '',
                selesai: checkboxInput && checkboxInput.checked ? '1' : '',
                catatan: catatanInput ? catatanInput.value : '',
            };
            promises.push(savePendingKriteria(payload.siswa_id, payload.mapel_id, payload.rombel_id, payload.kategori_id, payload.kriteria_id, payload.nilai, payload.selesai, payload.catatan));
        });
        Promise.all(promises).then(function() {
            finish(true, 'Server sibuk. Data tersimpan lokal.');
            updateGlobalSyncStatus('pending', 'Data menunggu sinkronisasi...');
        }).catch(function() {
            finish(false, 'Gagal menyimpan. Coba lagi.');
        });
    });
}

function updateGlobalSyncStatus(status, detail) {
    var el = document.getElementById('globalSyncStatus');
    if (!el) return;
    if (status === 'synced') {
        el.innerHTML = '<span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span> Semua tersinkronisasi';
        el.className = 'text-xs text-green-600 flex items-center gap-1.5 mt-1';
    } else if (status === 'pending') {
        el.innerHTML = '<span class="w-2 h-2 rounded-full bg-yellow-500 inline-block animate-pulse"></span> ' + (detail || 'Data menunggu sinkronisasi...');
        el.className = 'text-xs text-yellow-600 flex items-center gap-1.5 mt-1';
    } else if (status === 'offline') {
        el.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> Offline — data disimpan lokal';
        el.className = 'text-xs text-red-600 flex items-center gap-1.5 mt-1';
    } else if (status === 'syncing') {
        el.innerHTML = '<span class="w-2 h-2 rounded-full bg-blue-500 inline-block animate-pulse"></span> Menyinkronkan...';
        el.className = 'text-xs text-blue-600 flex items-center gap-1.5 mt-1';
    }
}

// Event delegation for autosave
document.addEventListener('input', function(e) {
    const row = e.target.closest('tr');
    if (!row) return;
    const container = row.closest('.tab-kategori-content');
    if (!container) return;
    const kategoriId = container.id.replace('panel-kategori-', '');
    if (!kategoriId || kategoriId === 'akhir') return;
    debounceRowSave(row, kategoriId);
});

document.addEventListener('change', function(e) {
    const row = e.target.closest('tr');
    if (!row) return;
    const container = row.closest('.tab-kategori-content');
    if (!container) return;
    const kategoriId = container.id.replace('panel-kategori-', '');
    if (!kategoriId || kategoriId === 'akhir') return;
    debounceRowSave(row, kategoriId);
});

// Prevent form submit for kategori tabs (selain akhir)
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (form.id && form.id.startsWith('formNilai-') && form.id !== 'formNilai-akhir') {
        e.preventDefault();
    }
    // Untuk form akhir, intercept when offline
    if (form.id === 'formNilai-akhir' && !navigator.onLine) {
        e.preventDefault();
        var formData = new FormData(form);
        if (window.offlineSubmit) window.offlineSubmit(form, formData);
    }
});

// Dengarkan pesan dari SW
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.addEventListener('message', function(event) {
        if (event.data.type === 'sync-success') {
            updateGlobalSyncStatus('synced');
            getPendingSyncCount().then(function(count) {
                if (count > 0) updateGlobalSyncStatus('pending', count + ' data menunggu sinkronisasi');
            });
        }
        if (event.data.type === 'sync-failed') {
            if (!navigator.onLine) {
                updateGlobalSyncStatus('offline');
            } else if (event.data.reason === 'auth') {
                updateGlobalSyncStatus('pending', 'Sesi habis. Silakan login ulang.');
            } else {
                updateGlobalSyncStatus('pending', 'Gagal sinkron. Data aman.');
            }
        }
    });
}

// Init tab aktif + cek pending items
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const kategoriFromUrl = urlParams.get('kategori');
    const firstId = <?= json_encode($firstKategoriId) ?>;

    if (kategoriFromUrl === 'akhir') {
        const btn = document.getElementById('tab-kategori-akhir');
        if (btn) switchTab('akhir');
    } else if (kategoriFromUrl) {
        const btn = document.getElementById('tab-kategori-' + kategoriFromUrl);
        if (btn) switchTab(parseInt(kategoriFromUrl));
    } else if (firstId && firstId !== 'akhir') {
        initGradeForm(firstId);
    }

    // Cek status IDB
    if (typeof getPendingSyncCount === 'function') {
        getPendingSyncCount().then(function(count) {
            if (count > 0) {
                updateGlobalSyncStatus('pending', count + ' data menunggu sinkronisasi');
                if (navigator.onLine) triggerSync();
            }
            if (!navigator.onLine) updateGlobalSyncStatus('offline');
        });
    }
});

// Deteksi offline
window.addEventListener('offline', function() {
    updateGlobalSyncStatus('offline');
});

window.addEventListener('online', function() {
    getPendingSyncCount().then(function(count) {
        if (count > 0) {
            updateGlobalSyncStatus('pending', count + ' data menunggu sinkronisasi');
            if (window.triggerSync) window.triggerSync();
        } else {
            updateGlobalSyncStatus('synced');
        }
    });
});
</script>
<?= $this->endSection() ?>
