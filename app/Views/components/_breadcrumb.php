<nav class="flex items-center gap-2 text-xs text-outline mb-4 min-h-[44px]" aria-label="Breadcrumb">
    <?php $total = count($items); ?>
    <?php foreach ($items as $i => $item): ?>
        <?php if ($i > 0): ?>
        <span class="text-outline/40 material-symbols-outlined text-sm">chevron_right</span>
        <?php endif; ?>
        <?php if (isset($item['url'])): ?>
        <a href="<?= esc($item['url']) ?>" class="hover:text-primary transition-colors py-2 <?= $i === $total - 1 ? 'text-primary font-bold' : '' ?>"<?= $i === $total - 1 ? ' aria-current="page"' : '' ?>>
            <?= esc($item['label']) ?>
        </a>
        <?php else: ?>
        <span class="py-2 <?= $i === $total - 1 ? 'text-primary font-bold' : '' ?>"<?= $i === $total - 1 ? ' aria-current="page"' : '' ?>>
            <?= esc($item['label']) ?>
        </span>
        <?php endif; ?>
    <?php endforeach; ?>
</nav>