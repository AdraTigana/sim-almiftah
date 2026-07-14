const DB_NAME = 'AlMiftahDB';
const DB_VERSION = 2;

const STORES = {
    PENDING_NILAI: 'pending_nilai',
    PENDING_PRESENSI: 'pending_presensi',
    PENDING_ADMIN: 'pending_admin',
    CACHED_API: 'cached_api',
    AUTH_SESSION: 'auth_session',
};

function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(DB_NAME, DB_VERSION);
        req.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORES.PENDING_NILAI)) {
                db.createObjectStore(STORES.PENDING_NILAI, { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains(STORES.PENDING_PRESENSI)) {
                db.createObjectStore(STORES.PENDING_PRESENSI, { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains(STORES.PENDING_ADMIN)) {
                db.createObjectStore(STORES.PENDING_ADMIN, { keyPath: 'local_id' });
            }
            if (!db.objectStoreNames.contains(STORES.CACHED_API)) {
                db.createObjectStore(STORES.CACHED_API, { keyPath: 'key' });
            }
            if (!db.objectStoreNames.contains(STORES.AUTH_SESSION)) {
                db.createObjectStore(STORES.AUTH_SESSION, { keyPath: 'key' });
            }
        };
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

// ─── Generic helpers ────────────────────────────────────────

async function addToStore(storeName, data) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    return new Promise((resolve, reject) => {
        const req = store.add(data);
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

async function putInStore(storeName, data) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    return new Promise((resolve, reject) => {
        const req = store.put(data);
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

async function getFromStore(storeName, key) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readonly');
    const store = tx.objectStore(storeName);
    return new Promise((resolve, reject) => {
        const req = store.get(key);
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

async function deleteFromStore(storeName, key) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    return new Promise((resolve, reject) => {
        const req = store.delete(key);
        req.onsuccess = () => resolve();
        req.onerror = () => reject(req.error);
    });
}

async function deleteMultipleFromStore(storeName, keys) {
    const db = await openDB();
    const tx = db.transaction(storeName, 'readwrite');
    const store = tx.objectStore(storeName);
    for (var i = 0; i < keys.length; i++) {
        store.delete(keys[i]);
    }
    return new Promise(function(resolve, reject) {
        tx.oncomplete = function() { resolve(); };
        tx.onerror = function() { reject(tx.error); };
    });
}

async function getCountFromStore(storeName) {
    const all = await getAllFromStore(storeName);
    return all.length;
}

function generateLocalId(prefix) {
    return prefix + '_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
}

// ─── pending_nilai (existing, preserved) ────────────────────

async function savePendingNilai(data) {
    data.local_id = generateLocalId('pend');
    data.saved_at = new Date().toISOString();
    return addToStore(STORES.PENDING_NILAI, data);
}

async function getAllPendingNilai() {
    return getAllFromStore(STORES.PENDING_NILAI);
}

async function deletePendingNilai(localId) {
    return deleteFromStore(STORES.PENDING_NILAI, localId);
}

async function getPendingCount() {
    return getCountFromStore(STORES.PENDING_NILAI);
}

async function savePendingKriteria(siswaId, mapelId, rombelId, jilidId, kriteriaId, nilai, selesai, catatan) {
    var data = {
        local_id: generateLocalId('krit'),
        type: 'kriteria',
        siswa_id: siswaId,
        mapel_id: mapelId,
        rombel_id: rombelId,
        jilid_id: jilidId,
        kriteria_id: kriteriaId,
        nilai: nilai || '',
        selesai: selesai || '',
        catatan: catatan || '',
        saved_at: new Date().toISOString(),
    };
    return addToStore(STORES.PENDING_NILAI, data);
}

async function getPendingSyncItems() {
    var all = await getAllFromStore(STORES.PENDING_NILAI);
    return all.filter(function(item) { return item.type === 'kriteria'; });
}

async function getPendingSyncCount() {
    var items = await getPendingSyncItems();
    return items.length;
}

async function deletePendingSyncItems(localIds) {
    return deleteMultipleFromStore(STORES.PENDING_NILAI, localIds);
}

// ─── pending_presensi (new) ─────────────────────────────────

async function savePendingPresensi(data) {
    data.local_id = generateLocalId('pres');
    data.saved_at = new Date().toISOString();
    return addToStore(STORES.PENDING_PRESENSI, data);
}

async function getAllPendingPresensi() {
    return getAllFromStore(STORES.PENDING_PRESENSI);
}

async function deletePendingPresensi(localId) {
    return deleteFromStore(STORES.PENDING_PRESENSI, localId);
}

async function getPendingPresensiCount() {
    return getCountFromStore(STORES.PENDING_PRESENSI);
}

// ─── pending_admin (new) ────────────────────────────────────

async function savePendingAdmin(method, endpoint, data) {
    var record = {
        local_id: generateLocalId('adm'),
        method: method,
        endpoint: endpoint,
        data: data,
        saved_at: new Date().toISOString(),
    };
    return addToStore(STORES.PENDING_ADMIN, record);
}

async function getAllPendingAdmin() {
    return getAllFromStore(STORES.PENDING_ADMIN);
}

async function deletePendingAdmin(localId) {
    return deleteFromStore(STORES.PENDING_ADMIN, localId);
}

async function getPendingAdminCount() {
    return getCountFromStore(STORES.PENDING_ADMIN);
}

// ─── cached_api (new) ───────────────────────────────────────

async function cacheApiResponse(key, responseData) {
    var record = {
        key: key,
        data: responseData,
        cached_at: new Date().toISOString(),
    };
    return putInStore(STORES.CACHED_API, record);
}

async function getCachedApiResponse(key) {
    return getFromStore(STORES.CACHED_API, key);
}

async function clearStaleCache(maxAgeHours) {
    maxAgeHours = maxAgeHours || 24;
    var all = await getAllFromStore(STORES.CACHED_API);
    var now = Date.now();
    for (var i = 0; i < all.length; i++) {
        var age = now - new Date(all[i].cached_at).getTime();
        if (age > maxAgeHours * 60 * 60 * 1000) {
            await deleteFromStore(STORES.CACHED_API, all[i].key);
        }
    }
}

// ─── auth_session (new) ─────────────────────────────────────

async function saveAuthSession(sessionData) {
    var record = {
        key: 'current_session',
        user_id: sessionData.user_id,
        role: sessionData.role,
        nama: sessionData.nama,
        email: sessionData.email,
        login_at: new Date().toISOString(),
    };
    return putInStore(STORES.AUTH_SESSION, record);
}

async function getAuthSession() {
    return getFromStore(STORES.AUTH_SESSION, 'current_session');
}

async function clearAuthSession() {
    return deleteFromStore(STORES.AUTH_SESSION, 'current_session');
}

// ─── Legacy alias ───────────────────────────────────────────
window.savePendingNilai = savePendingNilai;
window.getAllPendingNilai = getAllPendingNilai;
window.deletePendingNilai = deletePendingNilai;
window.getPendingCount = getPendingCount;
window.savePendingKriteria = savePendingKriteria;
window.getPendingSyncItems = getPendingSyncItems;
window.getPendingSyncCount = getPendingSyncCount;
window.deletePendingSyncItems = deletePendingSyncItems;
window.savePendingPresensi = savePendingPresensi;
window.getAllPendingPresensi = getAllPendingPresensi;
window.deletePendingPresensi = deletePendingPresensi;
window.getPendingPresensiCount = getPendingPresensiCount;
window.savePendingAdmin = savePendingAdmin;
window.getAllPendingAdmin = getAllPendingAdmin;
window.deletePendingAdmin = deletePendingAdmin;
window.getPendingAdminCount = getPendingAdminCount;
window.cacheApiResponse = cacheApiResponse;
window.getCachedApiResponse = getCachedApiResponse;
window.clearStaleCache = clearStaleCache;
window.saveAuthSession = saveAuthSession;
window.getAuthSession = getAuthSession;
window.clearAuthSession = clearAuthSession;
