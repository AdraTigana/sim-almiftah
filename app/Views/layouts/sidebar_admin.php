<aside class="bg-primary h-screen w-64 hidden lg:flex flex-col fixed left-0 top-0 shadow-lg lg:z-50 z-[60]" id="side-nav">
    <div class="flex flex-col h-full" style="padding: 16px 0;">
        <!-- Logo -->
        <div class="px-6 py-6 mb-2 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">mosque</span>
                </div>
                <div>
                    <h1 class="font-headline-md text-headline-md text-white leading-none">Al-Miftah</h1>
                    <p class="text-[10px] uppercase tracking-widest text-white/60 mt-1">Academic MIS</p>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden p-2 text-white/70 hover:text-white min-h-[44px] min-w-[44px] flex items-center justify-center">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <nav class="flex-1 flex flex-col gap-1 overflow-y-auto scrollbar-hide px-3">
            <!-- Dashboard -->
            <a href="<?= base_url('admin') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                      <?= (uri_string() == 'admin' || uri_string() == 'admin/dashboard') ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md">Dashboard</span>
            </a>

            <div class="mx-3 my-2 border-t border-white/15"></div>

            <!-- Akademik -->
            <span class="px-4 py-1.5 text-[10px] font-bold text-white/40 uppercase tracking-widest">Akademik</span>

            <a href="<?= base_url('admin/tahun-ajar') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                      <?= strpos(uri_string(), 'admin/tahun-ajar') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-label-md">Tahun Ajar</span>
            </a>

            <a href="<?= base_url('admin/rombel') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                       <?= strpos(uri_string(), 'admin/rombel') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">grid_view</span>
                <span class="font-label-md">Rombel</span>
            </a>

            <a href="<?= base_url('admin/kurikulum') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                      <?= strpos(uri_string(), 'admin/kurikulum') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">menu_book</span>
                <span class="font-label-md">Kurikulum</span>
            </a>

            <div class="mx-3 my-2 border-t border-white/15"></div>

            <!-- Master Data -->
            <span class="px-4 py-1.5 text-[10px] font-bold text-white/40 uppercase tracking-widest">Master Data</span>

            <a href="<?= base_url('admin/santri') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                      <?= strpos(uri_string(), 'admin/santri') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-md">Data Santri</span>
            </a>

            <a href="<?= base_url('admin/guru') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                       <?= strpos(uri_string(), 'admin/guru') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">badge</span>
                <span class="font-label-md">Data Guru</span>
            </a>

            <div class="mx-3 my-2 border-t border-white/15"></div>

            <a href="<?= base_url('admin/cetak') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                       <?= strpos(uri_string(), 'admin/cetak') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">print</span>
                <span class="font-label-md">Cetak Rapor</span>
            </a>

            <a href="<?= base_url('admin/profil') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 rounded-lg transition-all
                      <?= strpos(uri_string(), 'admin/profil') === 0 ? 'bg-white/10 text-accent font-bold' : 'text-white/70 hover:bg-white/10 hover:translate-x-1' ?>">
                <span class="material-symbols-outlined">person</span>
                <span class="font-label-md">Profil</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="border-t border-white/15 px-3 pt-3 pb-2">
            <a href="<?= base_url('auth/logout') ?>"
               class="flex items-center gap-3 min-h-[44px] px-4 py-2.5 text-white/70 hover:bg-white/10 hover:translate-x-1 rounded-lg transition-all font-semibold">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md">Keluar</span>
            </a>
        </div>
    </div>
</aside>
