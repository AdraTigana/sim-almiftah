const CACHE_APP_SHELL = 'almiftah-app-shell-v6';
const CACHE_DYNAMIC = 'almiftah-dynamic-v6';
const CACHE_CDN = 'almiftah-cdn-v6';

importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

if (workbox) {
    workbox.setConfig({ debug: false });
}

// ─── Cache Names ────────────────────────────────────────────
workbox.core.setCacheNameDetails({
    prefix: 'almiftah',
    suffix: 'v6',
    precache: CACHE_APP_SHELL,
    runtime: CACHE_DYNAMIC,
});

// ─── App Shell ──────────────────────────────────────────────
const APP_SHELL_URLS = [
    '/',
    '/manifest.json',
    '/offline.html',
    '/auth/login',
    '/icons/icon-48x48.png',
    '/icons/icon-72x72.png',
    '/icons/icon-96x96.png',
    '/icons/icon-128x128.png',
    '/icons/icon-192x192.png',
    '/icons/icon-192x192-maskable.png',
    '/icons/icon-512x512.png',
    '/icons/icon-512x512-maskable.png',
    '/assets/js/db.js',
    '/assets/css/tailwind.css',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_APP_SHELL).then((cache) => {
            return cache.addAll(APP_SHELL_URLS).catch(function(err) {
                console.warn('SW: partial precache failure:', err);
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    const validCaches = [CACHE_APP_SHELL, CACHE_DYNAMIC, CACHE_CDN];
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (!validCaches.includes(key)) return caches.delete(key);
                })
            );
        })
    );
    self.clients.claim();
});

// ─── Workbox Routing ────────────────────────────────────────

// CDN assets: cache-first (fonts, Material Icons, SweetAlert2)
workbox.routing.registerRoute(
    ({request}) => request.destination === 'font' ||
                    request.destination === 'style' ||
                    (request.url.includes('fonts.googleapis.com') ||
                     request.url.includes('fonts.gstatic.com') ||
                     request.url.includes('cdn.jsdelivr.net') ||
                     request.url.includes('googleapis.com')),
    new workbox.strategies.CacheFirst({
        cacheName: CACHE_CDN,
        plugins: [
            new workbox.expiration.ExpirationPlugin({
                maxEntries: 60,
                maxAgeSeconds: 30 * 24 * 60 * 60,
            }),
        ],
    })
);

// Static assets (images, CSS, JS from our server): cache-first
workbox.routing.registerRoute(
    ({request}) => request.destination === 'image' ||
                    request.destination === 'script' ||
                    (request.destination === 'style' &&
                     !request.url.includes('googleapis') &&
                     !request.url.includes('cdn.jsdelivr')),
    new workbox.strategies.CacheFirst({
        cacheName: CACHE_DYNAMIC,
        plugins: [
            new workbox.expiration.ExpirationPlugin({
                maxEntries: 100,
                maxAgeSeconds: 7 * 24 * 60 * 60,
            }),
        ],
    })
);

// Navigations (pages): network-first with 3s timeout, fallback to cache then offline.html
workbox.routing.registerRoute(
    ({request}) => request.mode === 'navigate',
    new workbox.strategies.NetworkFirst({
        cacheName: CACHE_DYNAMIC,
        networkTimeoutSeconds: 3,
        plugins: [
            new workbox.expiration.ExpirationPlugin({
                maxEntries: 50,
                maxAgeSeconds: 7 * 24 * 60 * 60,
            }),
            {
                handlerDidError: async () => {
                    return caches.match('/offline.html');
                },
            },
        ],
    })
);

// API-like GET requests: network-first
workbox.routing.registerRoute(
    ({url}) => url.pathname.includes('/guru/nilai/') ||
               url.pathname.includes('/api/') ||
               url.pathname.includes('/guru/presensi/'),
    new workbox.strategies.NetworkFirst({
        cacheName: CACHE_DYNAMIC,
        networkTimeoutSeconds: 4,
        plugins: [
            new workbox.expiration.ExpirationPlugin({
                maxEntries: 50,
                maxAgeSeconds: 24 * 60 * 60,
            }),
        ],
    })
);

// ─── Background Sync ────────────────────────────────────────

// Sync handler: replay pending mutations to server
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-nilai') {
        event.waitUntil(syncByType('pending_nilai', '/guru/nilai/sync-batch'));
    }
    if (event.tag === 'sync-presensi') {
        event.waitUntil(syncByType('pending_presensi', '/guru/presensi/sync-batch'));
    }
    if (event.tag === 'sync-admin') {
        event.waitUntil(syncAdminQueue());
    }
});

async function syncByType(storeName, endpoint) {
    try {
        const items = await getAllFromStore(storeName);
        if (items.length === 0) return;

        const payload = items.map(item => {
            const { local_id, saved_at, ...rest } = item;
            return rest;
        });

        const response = await fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-Offline-Sync': 'true',
            },
            body: JSON.stringify({ items: payload }),
        });

        if (response.ok) {
            const ids = items.map(item => item.local_id);
            await deleteFromStore(storeName, ids);
            await notifyClients({
                type: 'sync-success',
                store: storeName,
                count: items.length,
            });
        } else if (response.status === 401 || response.status === 403) {
            await notifyClients({ type: 'sync-failed', reason: 'auth', store: storeName });
        } else {
            await notifyClients({ type: 'sync-failed', reason: 'server', store: storeName });
        }
    } catch (e) {
        console.warn('Sync error for', storeName, ':', e);
        await notifyClients({ type: 'sync-failed', reason: 'network', store: storeName });
    }
}

async function syncAdminQueue() {
    try {
        const items = await getAllFromStore('pending_admin');
        if (items.length === 0) return;

        const results = [];
        for (const item of items) {
            try {
                // Send as form-urlencoded (admin controllers use getPost())
                var params = new URLSearchParams();
                for (var key in item.data) {
                    if (item.data.hasOwnProperty(key)) {
                        var val = item.data[key];
                        if (typeof val === 'object') {
                            for (var k2 in val) {
                                if (val.hasOwnProperty(k2)) {
                                    var arrVal = val[k2];
                                    if (Array.isArray(arrVal)) {
                                        arrVal.forEach(function(v) { params.append(key + '[' + k2 + '][]', v); });
                                    } else {
                                        params.append(key + '[' + k2 + ']', arrVal);
                                    }
                                }
                            }
                        } else {
                            params.append(key, val);
                        }
                    }
                }
                const response = await fetch(item.endpoint, {
                    method: item.method || 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Offline-Sync': 'true' },
                    body: params.toString(),
                });
                results.push({ local_id: item.local_id, ok: response.ok });
            } catch (e) {
                results.push({ local_id: item.local_id, ok: false });
            }
        }

        const okIds = results.filter(r => r.ok).map(r => r.local_id);
        if (okIds.length > 0) {
            await deleteFromStore('pending_admin', okIds);
        }

        const failCount = results.filter(r => !r.ok).length;
        if (failCount === 0) {
            await notifyClients({ type: 'sync-success', store: 'pending_admin', count: okIds.length });
        } else {
            await notifyClients({ type: 'sync-failed', reason: 'server', store: 'pending_admin' });
        }
    } catch (e) {
        console.warn('Admin sync error:', e);
        await notifyClients({ type: 'sync-failed', reason: 'network', store: 'pending_admin' });
    }
}

// ─── Message Listener ───────────────────────────────────────
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'sync') {
        event.waitUntil(Promise.all([
            syncByType('pending_nilai', '/guru/nilai/sync-batch'),
            syncByType('pending_presensi', '/guru/presensi/sync-batch'),
            syncAdminQueue(),
        ]));
    }
    if (event.data && event.data.type === 'register-sync') {
        const tag = event.data.tag;
        if (tag && 'sync' in self.registration) {
            event.waitUntil(self.registration.sync.register(tag));
        }
    }
});

// ─── IndexedDB Helpers ──────────────────────────────────────
function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('AlMiftahDB', 2);
        req.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('pending_nilai')) {
                db.createObjectStore('pending_nilai', { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains('pending_presensi')) {
                db.createObjectStore('pending_presensi', { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains('pending_admin')) {
                db.createObjectStore('pending_admin', { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains('cached_api')) {
                db.createObjectStore('cached_api', { keyPath: 'key' });
            }
            if (!db.objectStoreNames.contains('auth_session')) {
                db.createObjectStore('auth_session', { keyPath: 'key' });
            }
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

async function getAllFromStore(storeName) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readonly');
    const store = tx.objectStore(storeName);
    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

async function deleteFromStore(storeName, ids) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    for (const id of ids) {
        store.delete(id);
    }
    return new Promise((resolve, reject) => {
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
    });
}

async function notifyClients(message) {
    const clients = await self.clients.matchAll();
    clients.forEach((client) => client.postMessage(message));
}
