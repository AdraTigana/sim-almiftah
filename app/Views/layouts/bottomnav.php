<nav id="bottom-navigation" class="lg:hidden fixed bottom-0 left-0 right-0 bg-surface dark:bg-on-background border-t border-outline-variant/30 z-50 safe-area-bottom" style="padding-bottom: max(env(safe-area-inset-bottom), 0px);">
    <div class="flex justify-around items-center h-[68px] px-2">
        <?php $role = session()->get('role'); ?>
        <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('admin') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= uri_string() == 'admin' || uri_string() == 'admin/dashboard' ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= uri_string() == 'admin' || uri_string() == 'admin/dashboard' ? 'font-bold' : '' ?>">dashboard</span>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>
        <a href="<?= base_url('admin/santri') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'admin/santri') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'admin/santri') === 0 ? 'font-bold' : '' ?>">group</span>
            <span class="text-[10px] font-medium">Santri</span>
        </a>
        <a href="<?= base_url('admin/guru') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'admin/guru') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'admin/guru') === 0 ? 'font-bold' : '' ?>">badge</span>
            <span class="text-[10px] font-medium">Guru</span>
        </a>
        <a href="<?= base_url('admin/rombel') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'admin/rombel') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'admin/rombel') === 0 ? 'font-bold' : '' ?>">grid_view</span>
            <span class="text-[10px] font-medium">Rombel</span>
        </a>
        <a href="<?= base_url('admin/kurikulum') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'admin/kurikulum') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'admin/kurikulum') === 0 ? 'font-bold' : '' ?>">menu_book</span>
            <span class="text-[10px] font-medium">Kurikulum</span>
        </a>
        <a href="<?= base_url('admin/profil') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'admin/profil') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'admin/profil') === 0 ? 'font-bold' : '' ?>">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        <?php elseif ($role === 'guru'): ?>
        <a href="<?= base_url('guru') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= uri_string() == 'guru' || uri_string() == 'guru/dashboard' ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= uri_string() == 'guru' || uri_string() == 'guru/dashboard' ? 'font-bold' : '' ?>">dashboard</span>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>
        <a href="<?= base_url('guru/input-saya') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'guru/input-saya') === 0 || strpos(uri_string(), 'guru/nilai') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'guru/input-saya') === 0 || strpos(uri_string(), 'guru/nilai') === 0 ? 'font-bold' : '' ?>">edit_note</span>
            <span class="text-[10px] font-medium">Kelas</span>
        </a>
        <a href="<?= base_url('guru/profil') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'guru/profil') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'guru/profil') === 0 ? 'font-bold' : '' ?>">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        <?php elseif ($role === 'walas'): ?>
        <a href="<?= base_url('walas') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= uri_string() == 'walas' || uri_string() == 'walas/dashboard' ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= uri_string() == 'walas' || uri_string() == 'walas/dashboard' ? 'font-bold' : '' ?>">dashboard</span>
            <span class="text-[10px] font-medium">Dashboard</span>
        </a>
        <a href="<?= base_url('walas/rapor') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'walas/rapor') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'walas/rapor') === 0 ? 'font-bold' : '' ?>">auto_stories</span>
            <span class="text-[10px] font-medium">Rapor</span>
        </a>
        <a href="<?= base_url('walas/rekapitulasi') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'walas/rekapitulasi') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'walas/rekapitulasi') === 0 ? 'font-bold' : '' ?>">assessment</span>
            <span class="text-[10px] font-medium">Rekapitulasi</span>
        </a>
        <a href="<?= base_url('walas/profil') ?>" class="flex flex-col items-center justify-center gap-0.5 min-h-[56px] min-w-[64px] px-3 <?= strpos(uri_string(), 'walas/profil') === 0 ? 'text-primary' : 'text-on-surface-variant' ?> transition-all active:scale-90 duration-150">
            <span class="material-symbols-outlined <?= strpos(uri_string(), 'walas/profil') === 0 ? 'font-bold' : '' ?>">person</span>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
        <?php endif; ?>
    </div>
</nav>