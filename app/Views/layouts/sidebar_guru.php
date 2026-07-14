<aside class="bg-primary h-screen w-64 hidden lg:flex flex-col fixed left-0 top-0 shadow-lg lg:z-50 z-[60]" id="side-nav" style="padding: 16px 0;">
    <div class="px-6 py-6 mb-2 flex items-center justify-between">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                <span class="material-symbols-outlined text-accent text-sm" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <div>
                <h1 class="font-headline-sm text-headline-sm text-white leading-none">Al-Miftah</h1>
                <p class="text-[10px] uppercase tracking-widest text-white/60 mt-0.5">Ustadz</p>
            </div>
        </div>
        <button onclick="closeSidebar()" class="lg:hidden p-2 text-white/70 hover:text-white min-h-[44px] min-w-[44px] flex items-center justify-center">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>
    <nav class="flex flex-col gap-1 flex-1 px-3">
        <a class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 <?= (uri_string() == 'guru' || uri_string() == 'guru/dashboard') ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10' ?> rounded-lg cursor-pointer transition-all hover:translate-x-1" href="<?= base_url('guru') ?>">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
        </a>
        <a class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 <?= strpos(uri_string(), 'guru/input-saya') === 0 || strpos(uri_string(), 'guru/nilai') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10' ?> rounded-lg cursor-pointer transition-all hover:translate-x-1" href="<?= base_url('guru/input-saya') ?>">
            <span class="material-symbols-outlined">edit_note</span>
            <span class="font-label-md text-label-md">Kelas Saya</span>
        </a>
        <a class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 <?= strpos(uri_string(), 'guru/profil') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10' ?> rounded-lg cursor-pointer transition-all hover:translate-x-1" href="<?= base_url('guru/profil') ?>">
            <span class="material-symbols-outlined">person</span>
            <span class="font-label-md text-label-md">Profil</span>
        </a>
    </nav>
    <div class="mt-auto px-3 pt-3 pb-2 border-t border-white/15">
        <a class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 text-white/70 hover:bg-white/10 rounded-lg cursor-pointer transition-all" href="<?= base_url('auth/logout') ?>">
            <span class="material-symbols-outlined">logout</span>
            <span class="font-label-md text-label-md">Logout</span>
        </a>
    </div>
</aside>
