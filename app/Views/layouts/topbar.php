<header class="bg-glass-surface backdrop-blur-md sticky top-0 z-50 border-b border-glass-border shadow-sm shadow-primary/5 h-16 flex items-center justify-between px-4 md:px-8 w-full">
    <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="lg:hidden p-3 min-h-[44px] min-w-[44px] flex items-center justify-center text-primary active:scale-95 duration-200" onclick="toggleSidebar()">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 z-40 hidden transition-opacity duration-300" onclick="closeSidebar()"></div>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-primary" id="status-indicator"></span>
            <span class="font-label-md text-label-md text-primary font-bold" id="status-text">Online</span>
            <button id="syncButton" onclick="window.handleSync()" class="ml-2 p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-primary hover:bg-primary/10 rounded-lg active:scale-95 transition-all duration-150" title="Sinkronkan data offline">
                <span id="syncIcon" class="material-symbols-outlined text-sm">sync</span>
            </button>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="hidden md:flex flex-col items-end mr-2">
            <span class="font-label-md text-label-md text-primary font-bold"><?= esc(session()->get('nama') ?? 'User') ?></span>
            <span class="text-[10px] text-outline capitalize"><?= esc(session()->get('role') ?? '') ?></span>
        </div>
        <div class="flex items-center gap-1 md:gap-2">
            <div class="relative" id="profileDropdown">
                <button onclick="toggleProfileDropdown()" class="min-h-[44px] min-w-[44px] flex items-center justify-center active:scale-95 duration-200 cursor-pointer">
                    <div class="w-10 h-10 rounded-full border-2 border-primary/20 p-0.5 overflow-hidden hover:border-primary/40 transition-all">
                        <div class="w-full h-full rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                            <?= esc(strtoupper(substr(session()->get('nama') ?? 'U', 0, 1))) ?>
                        </div>
                    </div>
                </button>
                <div id="profileMenu" class="hidden absolute right-0 top-full mt-2 w-56 glass-card rounded-2xl overflow-hidden shadow-lg shadow-primary/10 border border-outline-variant/20 z-50 origin-top-right transition-all duration-200">
                    <div class="px-4 py-3 border-b border-outline-variant/10 bg-primary/5">
                        <p class="font-bold text-sm text-on-surface truncate"><?= esc(session()->get('nama') ?? 'User') ?></p>
                        <p class="text-[10px] text-outline capitalize"><?= esc(session()->get('role_nama') ?? session()->get('role') ?? '') ?></p>
                    </div>
                    <div class="py-1">
                        <?php $role = session()->get('role'); ?>
                        <?php if ($role === 'guru'): ?>
                        <a href="<?= base_url('guru/profil') ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-primary/5 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">person</span>
                            Profil Saya
                        </a>
                        <?php elseif ($role === 'walas'): ?>
                        <a href="<?= base_url('walas/profil') ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-primary/5 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">person</span>
                            Profil Saya
                        </a>
                        <?php elseif ($role === 'admin'): ?>
                        <a href="<?= base_url('admin/profil') ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-primary/5 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-sm">person</span>
                            Profil Saya
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="border-t border-outline-variant/10 py-1">
                        <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-3 px-4 py-2.5 text-sm text-error hover:bg-error/5 transition-colors">
                            <span class="material-symbols-outlined text-sm">logout</span>
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('side-nav');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar && backdrop) {
        sidebar.classList.toggle('hidden');
        backdrop.classList.toggle('hidden');
    }
}
function closeSidebar() {
    const sidebar = document.getElementById('side-nav');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (sidebar && backdrop) {
        sidebar.classList.add('hidden');
        backdrop.classList.add('hidden');
    }
}
function toggleProfileDropdown() {
    const menu = document.getElementById('profileMenu');
    if (menu) {
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            menu.classList.add('animate-fade-in');
        }
    }
}
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const menu = document.getElementById('profileMenu');
    if (dropdown && menu && !dropdown.contains(e.target)) {
        menu.classList.add('hidden');
    }
});
window.handleSync = async function() {
    var btn = document.getElementById('syncButton');
    var icon = document.getElementById('syncIcon');
    if (btn) btn.disabled = true;
    if (icon) icon.classList.add('animate-spin');
    try {
        if (typeof window.triggerSync === 'function') await window.triggerSync();
        if (typeof window.refreshSyncBadge === 'function') window.refreshSyncBadge();
        // Reload biar data terbaru dari server termuat
        location.reload();
    } catch (e) {
        if (icon) icon.classList.remove('animate-spin');
        if (btn) btn.disabled = false;
    }
};
function updateOnlineStatus() {
    const indicator = document.getElementById('status-indicator');
    const text = document.getElementById('status-text');
    if (navigator.onLine) {
        indicator.className = 'w-2 h-2 rounded-full bg-primary';
        text.textContent = 'Online';
        text.className = 'font-label-md text-label-md text-primary font-bold';
    } else {
        indicator.className = 'w-2 h-2 rounded-full bg-status-offline';
        text.textContent = 'Offline';
        text.className = 'font-label-md text-label-md text-status-offline font-bold';
    }
}
window.addEventListener('online', updateOnlineStatus);
window.addEventListener('offline', updateOnlineStatus);
updateOnlineStatus();
</script>