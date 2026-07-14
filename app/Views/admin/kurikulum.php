<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Kurikulum<?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_admin') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', [
    'items' => [
        ['label' => 'Dashboard', 'url' => base_url('admin')],
        ['label' => 'Kurikulum'],
    ]
]) ?>

<div class="glass-card rounded-3xl shadow-sm shadow-primary/5 overflow-hidden">
    <div class="flex border-b border-outline-variant/20 bg-surface-container-low/30 overflow-x-auto">
        <a href="?tab=mapel" id="tab-mapel-btn"
           role="tab"
           aria-selected="<?= ($tab ?? 'mapel') === 'mapel' ? 'true' : 'false' ?>"
           aria-controls="tab-mapel"
           class="tab-btn flex items-center gap-2 min-h-[44px] px-6 py-4 text-sm font-bold transition-all border-b-2 <?= ($tab ?? 'mapel') === 'mapel' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary' ?>">
            <span class="material-symbols-outlined text-sm">menu_book</span>
            Mata Pelajaran
            <span class="ml-1 px-2 py-0.5 bg-surface-container-low rounded-full text-[10px]"><?= count($mapel) ?></span>
        </a>
        <a href="?tab=kategori" id="tab-kategori-btn"
           role="tab"
           aria-selected="<?= ($tab ?? 'mapel') === 'kategori' ? 'true' : 'false' ?>"
           aria-controls="tab-kategori"
           class="tab-btn flex items-center gap-2 min-h-[44px] px-6 py-4 text-sm font-bold transition-all border-b-2 <?= ($tab ?? 'mapel') === 'kategori' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary' ?>">
            <span class="material-symbols-outlined text-sm">layers</span>
            Kategori
            <span class="ml-1 px-2 py-0.5 bg-surface-container-low rounded-full text-[10px]"><?= count($kategori) ?></span>
        </a>
        <a href="?tab=kriteria" id="tab-kriteria-btn"
           role="tab"
           aria-selected="<?= ($tab ?? 'mapel') === 'kriteria' ? 'true' : 'false' ?>"
           aria-controls="tab-kriteria"
           class="tab-btn flex items-center gap-2 min-h-[44px] px-6 py-4 text-sm font-bold transition-all border-b-2 <?= ($tab ?? 'mapel') === 'kriteria' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary' ?>">
            <span class="material-symbols-outlined text-sm">checklist</span>
            Kriteria Penilaian
            <span class="ml-1 px-2 py-0.5 bg-surface-container-low rounded-full text-[10px]"><?= count($kriteria) ?></span>
        </a>
    </div>

    <!-- Tab: Mapel -->
    <div id="tab-mapel" role="tabpanel" aria-labelledby="tab-mapel-btn" class="tab-content p-6 md:p-8 <?= ($tab ?? 'mapel') !== 'mapel' ? 'hidden' : '' ?>">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-sm text-on-surface">Mata Pelajaran</h4>
            <div class="flex items-center gap-3">
                <input type="text" id="filterMapel" placeholder="Cari mapel..."
                       class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20 w-40"/>
                <button onclick="showCreateMapel()" class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tambah
                </button>
            </div>
        </div>
        <div class="space-y-2" id="mapelList">
            <?php if (!empty($mapel)): foreach ($mapel as $m): ?>
            <div class="mapel-item flex items-center justify-between p-3.5 bg-surface-container-low/50 rounded-xl hover:bg-surface-container-low transition-colors"
                 data-nama="<?= esc(strtolower($m['nama'])) ?>">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-sm">menu_book</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm text-on-surface"><?= esc($m['nama']) ?></span>
                        <?php if ($m['singkatan']): ?><span class="text-xs text-outline ml-2">(<?= esc($m['singkatan']) ?>)</span><?php endif; ?>
                        <?php if (!($m['is_active'] ?? 1)): ?>
                        <span class="ml-2 px-2 py-0.5 bg-surface-variant rounded-full text-[10px] text-outline">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                </div>
                <button onclick="confirmDelete('<?= base_url('admin/kurikulum/mapel/delete/' . $m['id']) ?>', 'mapel')" class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-sm">delete</span>
                </button>
            </div>
            <?php endforeach; else: ?>
            <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada mata pelajaran</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Kategori -->
    <div id="tab-kategori" role="tabpanel" aria-labelledby="tab-kategori-btn" class="tab-content p-6 md:p-8 <?= ($tab ?? 'mapel') !== 'kategori' ? 'hidden' : '' ?>">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-sm text-on-surface">Kategori</h4>
            <div class="flex items-center gap-3">
                <select id="filterKategoriMapel" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Mapel</option>
                    <?php foreach ($mapel as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button onclick="showCreateKategori()" class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tambah
                </button>
            </div>
        </div>
        <div class="space-y-2" id="kategoriList">
            <?php if (!empty($kategori)): foreach ($kategori as $k): ?>
            <div class="kategori-item flex items-center justify-between p-3.5 bg-surface-container-low/50 rounded-xl hover:bg-surface-container-low transition-colors"
                 data-mapel-id="<?= $k['mapel_id'] ?>">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-sm">layers</span>
                    </div>
                    <div>
                        <span class="font-bold text-sm text-on-surface"><?= esc($k['nama']) ?></span>
                        <span class="text-xs text-outline ml-2">(<?= esc($k['mapel_nama']) ?>)</span>
                        <span class="text-[10px] text-outline ml-1">Urut: <?= $k['urutan'] ?></span>
                        <span class="text-[10px] ml-1 <?= ($k['hitung_kosong'] ?? 0) ? 'text-primary' : 'text-secondary' ?>">
                            <?= ($k['hitung_kosong'] ?? 0) ? '• Semua' : '• Terisi' ?>
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="showEditKategori(<?= $k['id'] ?>)" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button onclick="confirmDelete('<?= base_url('admin/kurikulum/kategori/delete/' . $k['id']) ?>', 'kategori')" class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada kategori</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab: Kriteria Penilaian -->
    <div id="tab-kriteria" role="tabpanel" aria-labelledby="tab-kriteria-btn" class="tab-content p-6 md:p-8 <?= ($tab ?? 'mapel') !== 'kriteria' ? 'hidden' : '' ?>">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-sm text-on-surface">Kriteria Penilaian</h4>
            <div class="flex items-center gap-3">
                <select id="filterKriteriaMapel" class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Mapel</option>
                    <?php foreach ($mapel as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select id="filterKriteriaKategori" disabled class="px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/20 opacity-50">
                    <option value="" disabled selected>Pilih Mapel dulu</option>
                </select>
                <button onclick="showCreateKriteria()" class="btn-primary flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl font-bold text-sm hover:shadow-lg transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tambah
                </button>
            </div>
        </div>
        <div class="space-y-2" id="kriteriaList">
            <?php if (!empty($kriteria)): ?>
            <div class="hidden md:grid grid-cols-12 gap-3 px-3.5 py-2 text-[10px] font-bold text-outline uppercase tracking-wider">
                <div class="col-span-5">Nama Kriteria</div>
                <div class="col-span-3">Mapel • Kategori</div>
                <div class="col-span-2">Bobot • Skala</div>
                <div class="col-span-2 text-right">Aksi</div>
            </div>
            <?php foreach ($kriteria as $k): ?>
            <div class="kriteria-item flex items-center justify-between p-3.5 bg-surface-container-low/50 rounded-xl hover:bg-surface-container-low transition-colors"
                 data-mapel-id="<?= $k['mapel_id'] ?>"
                 data-kategori-id="<?= $k['kategori_id'] ?? '' ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-sm">checklist</span>
                    </div>
                    <div class="min-w-0">
                        <span class="font-bold text-sm text-on-surface block truncate"><?= esc($k['nama']) ?></span>
                        <span class="text-xs text-outline md:hidden">
                            <?= esc($k['mapel_nama'] ?: '—') ?>
                            <?php if (!empty($k['kategori_nama'])): ?> • <?= esc($k['kategori_nama']) ?><?php endif; ?>
                            • <?= $k['bobot'] ?>% • Max: <?= $k['skala_max'] ?>
                        </span>
                        <span class="text-xs text-outline hidden md:inline">
                            <?= esc($k['mapel_nama'] ?: '—') ?>
                            <?php if (!empty($k['kategori_nama'])): ?> • <?= esc($k['kategori_nama']) ?><?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="hidden md:flex items-center gap-3 text-xs text-outline shrink-0">
                    <span><?= $k['bobot'] ?>%</span>
                    <span class="text-outline/40">|</span>
                    <span>Max: <?= $k['skala_max'] ?></span>
                    <span class="text-outline/40">|</span>
                    <span class="font-bold <?= match($k['input_type'] ?? 'number') { 'checkbox' => 'text-status-success', 'text' => 'text-secondary', default => 'text-primary' } ?>">
                        <?= match($k['input_type'] ?? 'number') { 'checkbox' => 'Ceklis', 'text' => 'Huruf', default => 'Angka' } ?>
                    </span>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button onclick="showEditKriteria(<?= $k['id'] ?>)" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">edit</span>
                    </button>
                    <button onclick="confirmDelete('<?= base_url('admin/kurikulum/kriteria/delete/' . $k['id']) ?>', 'kriteria')" class="p-2 text-error hover:bg-error/10 rounded-lg transition-colors">
                        <span class="material-symbols-outlined text-sm">delete</span>
                    </button>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada kriteria penilaian</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal: Tambah Mapel -->
<div id="createMapelModal" role="dialog" aria-modal="true" aria-labelledby="modal-mapel-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreateMapel()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h4 id="modal-mapel-title" class="font-headline-sm text-headline-sm text-primary mb-4">Tambah Mata Pelajaran</h4>
        <form method="POST" action="<?= base_url('admin/kurikulum/mapel/create') ?>" data-offline="true" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label for="mapelNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Mapel</label>
                <input type="text" name="nama" id="mapelNama" required placeholder="Nama mata pelajaran"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="mapelSingkatan" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Singkatan</label>
                <input type="text" name="singkatan" id="mapelSingkatan" placeholder="cth: NHW"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideCreateMapel()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tambah Kategori -->
<div id="createKategoriModal" role="dialog" aria-modal="true" aria-labelledby="modal-kategori-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreateKategori()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h4 id="modal-kategori-title" class="font-headline-sm text-headline-sm text-primary mb-4">Tambah Kategori</h4>
        <form method="POST" action="<?= base_url('admin/kurikulum/kategori/create') ?>" data-offline="true" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="filter_mapel" id="createFilterMapel" value=""/>
            <div>
                <label for="modalKategoriMapelId" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Mapel</label>
                <select name="mapel_id" id="modalKategoriMapelId" required class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Pilih Mapel</option>
                    <?php foreach ($mapel as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="modalKategoriNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Kategori</label>
                <input type="text" name="nama" id="modalKategoriNama" required placeholder="Nama kategori"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="modalKategoriUrutan" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Urutan</label>
                <input type="number" name="urutan" id="modalKategoriUrutan" placeholder="1"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="hitung_kosong" value="0"/>
                <input type="checkbox" name="hitung_kosong" id="modalHitungKosong" value="1"
                       class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30"/>
                <label for="modalHitungKosong" class="text-xs text-on-surface-variant cursor-pointer select-none">Hitung semua kategori (kosong = 0)</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideCreateKategori()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Kategori -->
<div id="editKategoriModal" role="dialog" aria-modal="true" aria-labelledby="modal-edit-kategori-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideEditKategori()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h4 id="modal-edit-kategori-title" class="font-headline-sm text-headline-sm text-primary mb-4">Edit Kategori</h4>
        <form method="POST" action="" id="editKategoriForm" data-offline="true" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="filter_mapel" id="editFilterMapel" value=""/>
            <div>
                <label for="editKategoriMapelId" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Mapel</label>
                <select name="mapel_id" id="editKategoriMapelId" required class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Pilih Mapel</option>
                    <?php foreach ($mapel as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="editKategoriNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Kategori</label>
                <input type="text" name="nama" id="editKategoriNama" required placeholder="Nama kategori"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div>
                <label for="editKategoriUrutan" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Urutan</label>
                <input type="number" name="urutan" id="editKategoriUrutan" placeholder="1"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="hitung_kosong" value="0"/>
                <input type="checkbox" name="hitung_kosong" id="editHitungKosong" value="1"
                       class="w-4 h-4 rounded border-outline-variant text-primary focus:ring-primary/30"/>
                <label for="editHitungKosong" class="text-xs text-on-surface-variant cursor-pointer select-none">Hitung semua kategori (kosong = 0)</label>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideEditKategori()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tambah Kriteria -->
<div id="createKriteriaModal" role="dialog" aria-modal="true" aria-labelledby="modal-kriteria-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideCreateKriteria()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h4 id="modal-kriteria-title" class="font-headline-sm text-headline-sm text-primary mb-4">Tambah Kriteria Penilaian</h4>
        <form method="POST" action="<?= base_url('admin/kurikulum/kriteria/create') ?>" data-offline="true" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="filter_mapel" id="createFilterMapelKriteria" value=""/>
            <input type="hidden" name="filter_kategori" id="createFilterKategori" value=""/>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="modalKriteriaMapel" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Mapel</label>
                    <select name="mapel_id" id="modalKriteriaMapel" required
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Pilih Mapel</option>
                        <?php foreach ($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="modalKriteriaKategori" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Kategori (opsional)</label>
                    <select name="kategori_id" id="modalKriteriaKategori" disabled
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="" disabled selected>Pilih Mapel dulu</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="modalKriteriaNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Kriteria</label>
                <input type="text" name="nama" id="modalKriteriaNama" required placeholder="Nama kriteria"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="modalKriteriaInputType" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Jenis Penilaian</label>
                    <select name="input_type" id="modalKriteriaInputType"
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="number">Angka</option>
                        <option value="text">Huruf</option>
                        <option value="checkbox">Ceklis</option>
                    </select>
                </div>
                <div>
                    <label for="modalKriteriaBobot" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Bobot (%)</label>
                    <input type="number" name="bobot" id="modalKriteriaBobot" value="100" placeholder="Bobot"
                           class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div>
                    <label for="modalKriteriaSkalaMax" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Skala Maks</label>
                    <input type="number" name="skala_max" id="modalKriteriaSkalaMax" value="100" placeholder="Skala"
                           class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideCreateKriteria()">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Kriteria -->
<div id="editKriteriaModal" role="dialog" aria-modal="true" aria-labelledby="modal-edit-kriteria-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hideEditKriteria()">
    <div class="glass-card rounded-3xl p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h4 id="modal-edit-kriteria-title" class="font-headline-sm text-headline-sm text-primary mb-4">Edit Kriteria Penilaian</h4>
        <form method="POST" action="" id="editKriteriaForm" data-offline="true" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="filter_mapel" id="editFilterMapelKriteria" value=""/>
            <input type="hidden" name="filter_kategori" id="editFilterKategori" value=""/>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="editKriteriaMapel" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Mapel</label>
                    <select name="mapel_id" id="editKriteriaMapel" required
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="">Pilih Mapel</option>
                        <?php foreach ($mapel as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="editKriteriaKategori" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Kategori (opsional)</label>
                    <select name="kategori_id" id="editKriteriaKategori" disabled
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="" disabled selected>Pilih Mapel dulu</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="editKriteriaNama" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Nama Kriteria</label>
                <input type="text" name="nama" id="editKriteriaNama" required placeholder="Nama kriteria"
                       class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label for="editKriteriaInputType" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Jenis Penilaian</label>
                    <select name="input_type" id="editKriteriaInputType"
                            class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20">
                        <option value="number">Angka</option>
                        <option value="text">Huruf</option>
                        <option value="checkbox">Ceklis</option>
                    </select>
                </div>
                <div>
                    <label for="editKriteriaBobot" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Bobot (%)</label>
                    <input type="number" name="bobot" id="editKriteriaBobot" placeholder="Bobot"
                           class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div>
                    <label for="editKriteriaSkalaMax" class="text-[10px] font-bold text-outline uppercase tracking-wider block mb-1">Skala Maks</label>
                    <input type="number" name="skala_max" id="editKriteriaSkalaMax" placeholder="Skala"
                           class="w-full px-4 py-3 bg-surface border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm">Simpan</button>
                <button type="button" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm" onclick="hideEditKriteria()">Batal</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(url, type) {
    safeConfirm('Hapus ' + type + '?', 'Yakin ingin menghapus ' + type + ' ini?', 'Ya, Hapus', 'Batal', function() {
        if (!navigator.onLine) {
            savePendingAdmin('POST', url, {}).then(function() { safeNotify('Success', 'Permintaan hapus tersimpan lokal.'); });
            return;
        }
        safeBlock('.glass-card');
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) { safeNotify('Success', res.message); setTimeout(() => location.reload(), 1000); }
            else { safeNotify('Failure', res.message || 'Gagal menghapus'); }
        })
        .catch(function() { savePendingAdmin('POST', url, {}).then(function() { safeNotify('Success', 'Permintaan hapus tersimpan lokal.'); }); })
        .finally(() => safeUnblock('.glass-card'));
    });
}

// --- Modal helpers ---
function toggleModal(id, show) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('hidden', !show);
    el.classList.toggle('flex', show);
}

// --- Mapel ---
function showCreateMapel() { toggleModal('createMapelModal', true); }
function hideCreateMapel() { toggleModal('createMapelModal', false); }

document.getElementById('filterMapel')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.mapel-item').forEach(el => {
        el.style.display = !q || (el.dataset.nama || '').includes(q) ? '' : 'none';
    });
});

// --- Kategori ---
function showCreateKategori() {
    document.getElementById('createFilterMapel').value = document.getElementById('filterKategoriMapel')?.value || '';
    toggleModal('createKategoriModal', true);
}
function hideCreateKategori() { toggleModal('createKategoriModal', false); }

document.getElementById('filterKategoriMapel')?.addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('.kategori-item').forEach(el => {
        el.style.display = !val || el.dataset.mapelId === val ? '' : 'none';
    });
});

// --- Edit Kategori ---
function showEditKategori(id) {
    toggleModal('editKategoriModal', true);
    document.getElementById('editFilterMapel').value = document.getElementById('filterKategoriMapel')?.value || '';
    const form = document.getElementById('editKategoriForm');
    form.action = '<?= base_url('admin/kurikulum/kategori/update') ?>/' + id;
    safeBlock('.glass-card');
    fetch('<?= base_url('admin/kurikulum/kategori/get') ?>/' + id)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data) {
                document.getElementById('editKategoriMapelId').value = res.data.mapel_id;
                document.getElementById('editKategoriNama').value = res.data.nama;
                document.getElementById('editKategoriUrutan').value = res.data.urutan;
                document.getElementById('editHitungKosong').checked = (res.data.hitung_kosong == 1);
            }
        })
        .catch(function() { safeNotify('Failure', 'Gagal memuat data kategori.'); })
        .finally(() => safeUnblock('.glass-card'));
}
function hideEditKategori() { toggleModal('editKategoriModal', false); }

// --- Kriteria ---
function showCreateKriteria() {
    document.getElementById('createFilterMapelKriteria').value = document.getElementById('filterKriteriaMapel')?.value || '';
    document.getElementById('createFilterKategori').value = document.getElementById('filterKriteriaKategori')?.value || '';
    toggleModal('createKriteriaModal', true);
}
function hideCreateKriteria() { toggleModal('createKriteriaModal', false); }

function filterKriteria() {
    const mapelVal = document.getElementById('filterKriteriaMapel')?.value || '';
    const kategoriVal = document.getElementById('filterKriteriaKategori')?.value || '';
    document.querySelectorAll('.kriteria-item').forEach(el => {
        const matchMapel = !mapelVal || el.dataset.mapelId === mapelVal;
        const matchKategori = !kategoriVal || el.dataset.kategoriId === kategoriVal;
        el.style.display = matchMapel && matchKategori ? '' : 'none';
    });
}
// Build lookup map: mapel_id -> [{ id, nama }]
const kategoriMap = {};
<?php foreach ($kategori as $kt): ?>
(function(){
    const mid = <?= $kt['mapel_id'] ?>;
    if (!kategoriMap[mid]) kategoriMap[mid] = [];
    kategoriMap[mid].push({ id: <?= $kt['id'] ?>, nama: '<?= esc($kt['nama'], 'js') ?>' });
})();
<?php endforeach; ?>

function rebuildKategoriOptions() {
    const sel = document.getElementById('filterKriteriaKategori');
    const mapelId = document.getElementById('filterKriteriaMapel')?.value;
    sel.innerHTML = '';
    if (!mapelId) {
        sel.disabled = true;
        sel.classList.add('opacity-50');
        sel.innerHTML = '<option value="" disabled selected>Pilih Mapel dulu</option>';
        return;
    }
    sel.disabled = false;
    sel.classList.remove('opacity-50');
    const first = document.createElement('option');
    first.value = '';
    first.textContent = 'Semua Kategori';
    sel.appendChild(first);
    (kategoriMap[mapelId] || []).forEach(k => {
        const opt = document.createElement('option');
        opt.value = k.id;
        opt.textContent = k.nama;
        sel.appendChild(opt);
    });
}

document.getElementById('filterKriteriaMapel')?.addEventListener('change', function() {
    rebuildKategoriOptions();
    filterKriteria();
});
document.getElementById('filterKriteriaKategori')?.addEventListener('change', filterKriteria);

// Rebuild kategori dropdown di modal berdasarkan mapel yang dipilih
function rebuildModalKategori(mapelId, kategoriId) {
    const sel = document.getElementById(kategoriId);
    const mapelVal = document.getElementById(mapelId)?.value;
    sel.innerHTML = '';
    if (!mapelVal) {
        sel.disabled = true;
        sel.innerHTML = '<option value="" disabled selected>Pilih Mapel dulu</option>';
        return;
    }
    sel.disabled = false;
    const first = document.createElement('option');
    first.value = '';
    first.textContent = 'Semua Kategori';
    sel.appendChild(first);
    (kategoriMap[mapelVal] || []).forEach(k => {
        const opt = document.createElement('option');
        opt.value = k.id;
        opt.textContent = k.nama;
        sel.appendChild(opt);
    });
}
document.getElementById('modalKriteriaMapel')?.addEventListener('change', function() {
    rebuildModalKategori('modalKriteriaMapel', 'modalKriteriaKategori');
});
document.getElementById('editKriteriaMapel')?.addEventListener('change', function() {
    rebuildModalKategori('editKriteriaMapel', 'editKriteriaKategori');
});

// Restore filter dari URL setelah redirect
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    var fm;

    if (tab === 'kategori' || !tab) {
        fm = urlParams.get('filter_mapel');
        if (fm) {
            document.getElementById('filterKategoriMapel').value = fm;
            document.getElementById('filterKategoriMapel').dispatchEvent(new Event('change'));
        }
    }
    if (tab === 'kriteria') {
        fm = urlParams.get('filter_mapel');
        if (fm) {
            document.getElementById('filterKriteriaMapel').value = fm;
            document.getElementById('filterKriteriaMapel').dispatchEvent(new Event('change'));
        }
        var fk = urlParams.get('filter_kategori');
        if (fk) {
            document.getElementById('filterKriteriaKategori').value = fk;
            document.getElementById('filterKriteriaKategori').dispatchEvent(new Event('change'));
        }
    }
});

// --- Edit Kriteria ---
function showEditKriteria(id) {
    toggleModal('editKriteriaModal', true);
    document.getElementById('editFilterMapelKriteria').value = document.getElementById('filterKriteriaMapel')?.value || '';
    document.getElementById('editFilterKategori').value = document.getElementById('filterKriteriaKategori')?.value || '';
    const form = document.getElementById('editKriteriaForm');
    form.action = '<?= base_url('admin/kurikulum/kriteria/update') ?>/' + id;
    safeBlock('.glass-card');
    fetch('<?= base_url('admin/kurikulum/kriteria/get') ?>/' + id)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data) {
                document.getElementById('editKriteriaMapel').value = res.data.mapel_id;
                document.getElementById('editKriteriaMapel').dispatchEvent(new Event('change'));
                document.getElementById('editKriteriaKategori').value = res.data.kategori_id || '';
                document.getElementById('editKriteriaNama').value = res.data.nama;
                document.getElementById('editKriteriaInputType').value = res.data.input_type || 'number';
                document.getElementById('editKriteriaBobot').value = res.data.bobot;
                document.getElementById('editKriteriaSkalaMax').value = res.data.skala_max;
            }
        })
        .catch(function() { safeNotify('Failure', 'Gagal memuat data kriteria.'); })
        .finally(() => safeUnblock('.glass-card'));
}
function hideEditKriteria() { toggleModal('editKriteriaModal', false); }
</script>
<?= $this->endSection() ?>
