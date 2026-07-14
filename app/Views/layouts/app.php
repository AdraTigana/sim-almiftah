<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= $this->renderSection('title') ?> - Al-Miftah MIS</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/tailwind.css') ?>"/>
    <style>select{padding-right:2.5rem!important}</style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('assets/js/db.js') ?>"></script>
    <link rel="manifest" href="<?= base_url('manifest.json') ?>"/>
    <meta name="theme-color" content="#046C4E"/>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="default"/>
    <link rel="apple-touch-icon" href="<?= base_url('icons/icon-192x192-maskable.png') ?>"/>
    <link rel="apple-touch-icon" sizes="512x512" href="<?= base_url('icons/icon-512x512-maskable.png') ?>"/>
    <?= $this->renderSection('head') ?>
</head>
<body class="bg-background text-on-surface font-body-md overflow-x-hidden selection:bg-primary/20 selection:text-primary">

<?= $this->renderSection('sidebar') ?>

<main class="lg:ml-64 min-h-screen flex flex-col">
    <?= $this->include('layouts/topbar') ?>

    <div class="px-4 md:px-8 py-8 max-w-7xl mx-auto w-full space-y-8">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="mt-auto py-8 px-4 md:px-8 border-t border-outline-variant/20 text-center">
        <p class="text-on-surface-variant text-xs">© <?= date('Y') ?> Sistem Informasi Al-Miftah - Pondok Pesantren MTI Canduang</p>
    </footer>
</main>

<?= $this->include('layouts/bottomnav') ?>

<?php if (session()->get('role') === 'guru'): ?>
<button class="lg:hidden fixed right-6 bottom-24 w-14 h-14 bg-primary text-white rounded-full shadow-lg flex items-center justify-center active:scale-90 transition-transform z-40" onclick="window.location.href='<?= base_url('guru/input-saya') ?>'">
    <span class="material-symbols-outlined" style="font-size:24px">edit_note</span>
</button>
<?php endif; ?>

<button id="btnInstallFloating" class="btn-primary hidden fixed right-6 bottom-36 z-50 py-3 px-5 bg-primary text-on-primary rounded-full font-bold text-sm shadow-lg hover:shadow-xl active:scale-95 transition-all flex items-center gap-2"
        onclick="deferredPrompt?.prompt(); deferredPrompt = null;">
    <span class="material-symbols-outlined text-sm">install_mobile</span>
    Install Aplikasi
</button>

<!-- Sync Pending Badge -->
<button id="syncPendingBadge" onclick="window.triggerSync()"
        class="hidden fixed right-6 bottom-52 z-50 py-2.5 px-4 bg-amber-500 text-white rounded-full font-bold text-xs shadow-lg hover:bg-amber-600 active:scale-95 transition-all flex items-center gap-2">
    <span class="material-symbols-outlined text-sm">sync</span>
    <span id="syncPendingCount">0</span>
    <span>pending</span>
</button>

<script>
    function safeNotify(type, message) {
        try {
            const icons = { Success:'success', Failure:'error', Warning:'warning', Info:'info' };
            Swal.fire({ toast:true, position:'top-end', icon:icons[type]||'info',
                title:message, showConfirmButton:false, timer:3000, timerProgressBar:true });
        } catch(e) { alert(message); }
    }
    function safeBlock(selector) {
        const el = document.querySelector(selector);
        if (!el) return;
        el.style.position = 'relative';
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner"></div>';
        el.appendChild(overlay);
    }
    function safeUnblock(selector) {
        const el = document.querySelector(selector);
        if (!el) return;
        const overlay = el.querySelector('.loading-overlay');
        if (overlay) overlay.remove();
    }
    function safeConfirm(title, message, okText, cancelText, okCallback) {
        try {
            Swal.fire({ title, html:message, icon:'warning', showCancelButton:true,
                confirmButtonColor:'#DC2626', cancelButtonColor:'#6B7280',
                confirmButtonText:okText, cancelButtonText: cancelText,
                reverseButtons:true })
            .then(r => { if (r.isConfirmed) okCallback(); });
        } catch(e) { if (confirm(message)) okCallback(); }
    }

    <?php if (session()->getFlashdata('success')): ?>
    safeNotify('Success', <?= json_encode(session()->getFlashdata('success'), JSON_HEX_TAG | JSON_HEX_APOS) ?>);
    <?php elseif (session()->getFlashdata('error')): ?>
    safeNotify('Failure', <?= json_encode(session()->getFlashdata('error'), JSON_HEX_TAG | JSON_HEX_APOS) ?>);
    <?php elseif (session()->getFlashdata('message')): ?>
    safeNotify('Success', <?= json_encode(session()->getFlashdata('message'), JSON_HEX_TAG | JSON_HEX_APOS) ?>);
    <?php endif; ?>

    // Global error handler
    window.addEventListener('error', function(e) {
        console.warn('Uncaught error:', e.message, 'at', e.filename, 'line', e.lineno);
    });

    document.querySelectorAll('button, a').forEach(el => {
        el.addEventListener('mousedown', () => el.classList.add('scale-95'));
        el.addEventListener('mouseup', () => el.classList.remove('scale-95'));
        el.addEventListener('mouseleave', () => el.classList.remove('scale-95'));
    });

    // Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('<?= base_url('sw.js') ?>?v=5')
                .then(reg => {
                    reg.addEventListener('updatefound', () => {
                        const newSW = reg.installing;
                        newSW.addEventListener('statechange', () => {
                            if (newSW.state === 'installed' && navigator.serviceWorker.controller) {
                                safeNotify('Info', 'Update tersedia. Tutup & buka ulang untuk menerapkan.');
                            }
                        });
                    });
                })
                .catch(err => console.warn('SW registration failed:', err));
        });
    }

    // Offline form submission helper (untuk form akhir)
    window.offlineSubmit = async function(form, formData) {
        const endpoint = form.getAttribute('action') || './guru/nilai/save-akhir';
        const data = {
            local_id: 'akhir_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8),
            type: 'akhir',
            endpoint: endpoint,
            siswa_id: formData.get('siswa_id') || '',
            mapel_id: formData.get('mapel_id') || '',
            rombel_id: formData.get('rombel_id') || '',
            mode: formData.get('mode') || '',
            kategori_data: {},
        };
        for (const [key, val] of formData.entries()) {
            if (!key.startsWith('csrf') && !['siswa_id', 'mapel_id', 'rombel_id', 'mode'].includes(key)) {
                data.kategori_data[key] = val;
            }
        }
        await savePendingNilai(data);
        safeNotify('Info', 'Data akhir tersimpan lokal. Akan dikirim otomatis saat koneksi pulih.');
    };

    // Kirim data offline presensi ke server (langsung dari page)
    window.syncPendingPresensi = async function() {
        try {
            if (typeof getAllPendingPresensi !== 'function') return true;
            var allItems = await getAllPendingPresensi();
            if (allItems.length === 0) return true;
            // Gunakan syncBatch endpoint yang sudah ada (JSON batch)
            var items = allItems.map(function(item) {
                return {
                    siswa_id: item.siswa_id,
                    rombel_id: item.rombel_id,
                    mapel_id: item.mapel_id,
                    tanggal: item.tanggal,
                    status: item.status,
                    keterangan: item.keterangan || '',
                };
            });
            var resp = await fetch('<?= base_url('guru/presensi/sync-batch') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'X-Offline-Sync': 'true' },
                credentials: 'same-origin',
                body: JSON.stringify({ items: items }),
            });
            var data = await resp.json();
            if (data.success) {
                for (var i = 0; i < allItems.length; i++) {
                    await deletePendingPresensi(allItems[i].local_id);
                }
                return true;
            }
            return false;
        } catch (e) {
            console.warn('Sync pending presensi failed:', e);
            return false;
        }
    };

    // Kirim data offline admin ke server (langsung dari page)
    window.syncPendingAdmin = async function() {
        try {
            if (typeof getAllPendingAdmin !== 'function') return true;
            var allItems = await getAllPendingAdmin();
            if (allItems.length === 0) return true;
            var ok = 0;
            for (var i = 0; i < allItems.length; i++) {
                var item = allItems[i];
                try {
                    var resp = await fetch(item.endpoint, {
                        method: item.method || 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Offline-Sync': 'true' },
                        credentials: 'same-origin',
                        body: JSON.stringify(item.data),
                    });
                    if (resp.ok) {
                        await deletePendingAdmin(item.local_id);
                        ok++;
                    }
                } catch (e) { /* will retry later */ }
            }
            if (ok > 0 && window.refreshSyncBadge) refreshSyncBadge();
            return ok === allItems.length;
        } catch (e) {
            console.warn('Sync pending admin failed:', e);
            return false;
        }
    };

    // Kirim data offline nilai ke server (langsung dari page, fallback jika SW tidak tersedia)
    window.syncPendingData = async function() {
        try {
            const allItems = await getAllPendingNilai();
            const kriteriaItems = allItems.filter(function(item) { return item.type === 'kriteria'; });
            if (kriteriaItems.length === 0) return true;

            const items = kriteriaItems.map(function(item) {
                return {
                    siswa_id: item.siswa_id,
                    mapel_id: item.mapel_id,
                    kategori_id: item.kategori_id,
                    rombel_id: item.rombel_id,
                    kriteria_id: item.kriteria_id,
                    nilai: item.nilai || '',
                    selesai: item.selesai || '',
                    catatan: item.catatan || '',
                };
            });

            var baseUrl = '<?= base_url() ?>';
            var resp = await fetch(baseUrl + 'guru/nilai/sync-batch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Offline-Sync': 'true' },
                credentials: 'same-origin',
                body: JSON.stringify({ items: items }),
            });
            if (resp.ok) {
                var ids = kriteriaItems.map(function(item) { return item.local_id; });
                await deletePendingSyncItems(ids);
                return true;
            }
            return false;
        } catch (e) {
            console.warn('Sync pending data failed:', e);
            return false;
        }
    };

    // Trigger sync: page-level dulu (lebih reliable), SW Background Sync sebagai backup
    window.triggerSync = async function() {
        await Promise.allSettled([
            typeof window.syncPendingData === 'function' ? window.syncPendingData() : Promise.resolve(),
            typeof window.syncPendingPresensi === 'function' ? window.syncPendingPresensi() : Promise.resolve(),
            typeof window.syncPendingAdmin === 'function' ? window.syncPendingAdmin() : Promise.resolve(),
        ]);
        if (window.refreshSyncBadge) refreshSyncBadge();
        // Backup: trigger SW Background Sync
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            try {
                var reg = await navigator.serviceWorker.ready;
                if ('sync' in reg) {
                    reg.sync.register('sync-nilai');
                    reg.sync.register('sync-presensi');
                    reg.sync.register('sync-admin');
                } else {
                    navigator.serviceWorker.controller.postMessage({ type: 'sync' });
                }
            } catch (e) { /* ignore */ }
        }
    };

    // Sync badge helper
    window.updateSyncBadge = function(count) {
        var badge = document.getElementById('syncPendingBadge');
        var countEl = document.getElementById('syncPendingCount');
        if (!badge || !countEl) return;
        if (count > 0) {
            badge.classList.remove('hidden');
            countEl.textContent = count;
        } else {
            badge.classList.add('hidden');
        }
    };

    // Cek total pending count on load
    window.refreshSyncBadge = function() {
        var total = 0;
        var checks = [];
        if (typeof getPendingCount === 'function') checks.push(getPendingCount());
        if (typeof getPendingPresensiCount === 'function') checks.push(getPendingPresensiCount());
        if (typeof getPendingAdminCount === 'function') checks.push(getPendingAdminCount());
        if (checks.length > 0) {
            Promise.all(checks).then(function(counts) {
                counts.forEach(function(c) { total += c; });
                updateSyncBadge(total);
            });
        }
    };
    refreshSyncBadge();

    // Auto-sync pending data on page load (when online)
    if (navigator.onLine && typeof window.triggerSync === 'function') {
        window.triggerSync();
    }

    // Dengarkan pesan dari SW
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.addEventListener('message', function(event) {
            if (event.data.type === 'sync-success') {
                var msg = event.data.count + ' data berhasil tersinkronisasi.';
                if (event.data.store === 'pending_presensi') msg = 'Presensi tersinkronisasi.';
                if (event.data.store === 'pending_admin') msg = 'Data admin tersinkronisasi.';
                safeNotify('Success', msg);
                if (window.refreshSyncBadge) refreshSyncBadge();
            }
            if (event.data.type === 'sync-failed' && event.data.reason !== 'auth') {
                safeNotify('Warning', 'Sinkronisasi gagal. Data tetap tersimpan lokal.');
                if (window.refreshSyncBadge) refreshSyncBadge();
            }
        });
    }

    // Deteksi offline
    window.addEventListener('offline', function() {
        safeNotify('Info', 'Anda sedang offline. Data akan disimpan lokal dan sinkron otomatis saat online.');
    });

    // Trigger sync when coming back online
    window.addEventListener('online', function() {
        if (typeof window.triggerSync === 'function') window.triggerSync().then(function() {
            if (typeof window.refreshSyncBadge === 'function') window.refreshSyncBadge();
        });
        safeNotify('Info', 'Koneksi tersambung kembali.');
    });

    // PWA Install
    var deferredPrompt = null;
    var btnInstallFloating = document.getElementById('btnInstallFloating');

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        if (btnInstallFloating) btnInstallFloating.classList.remove('hidden');
    });

    window.addEventListener('appinstalled', function() {
        deferredPrompt = null;
        if (btnInstallFloating) btnInstallFloating.classList.add('hidden');
    });

    if (window.matchMedia('(display-mode: standalone)').matches) {
        if (btnInstallFloating) btnInstallFloating.classList.add('hidden');
    }

    // Save session to IDB untuk offline access
    <?php if (session()->get('isLoggedIn')): ?>
    if (typeof saveAuthSession === 'function') {
        saveAuthSession({
            user_id: <?= json_encode(session()->get('userId')) ?>,
            role: <?= json_encode(session()->get('role')) ?>,
            nama: <?= json_encode(session()->get('nama')) ?>,
            email: <?= json_encode(session()->get('email')) ?>,
        });
    }
    <?php endif; ?>

    // Offline CRUD form handler untuk admin pages
    window.offlineFormHandler = function(form) {
        if (navigator.onLine) return true;

        var formData = new FormData(form);
        var data = {};
        formData.forEach(function(value, key) {
            data[key] = value; // include csrf token (stays valid with $regenerate=false)
        });

        savePendingAdmin(
            form.method || 'POST',
            form.action,
            data
        ).then(function() {
            safeNotify('Success', 'Data tersimpan lokal. Akan dikirim saat online.');
        }).catch(function() {
            safeNotify('Failure', 'Gagal menyimpan data lokal.');
        });

        return false;
    };

    // Auto-intercept admin form submissions when offline
    document.addEventListener('submit', function(e) {
        var form = e.target;
        if (form.hasAttribute('data-offline') && !navigator.onLine) {
            e.preventDefault();
            window.offlineFormHandler(form);
        }
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
