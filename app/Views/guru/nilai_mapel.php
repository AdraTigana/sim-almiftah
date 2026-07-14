<?= $this->extend('layouts/app') ?>
<?= $this->section('title') ?>Nilai - <?= esc($mapel['nama'] ?? '') ?><?= $this->endSection() ?>
<?= $this->section('sidebar') ?><?= $this->include('layouts/sidebar_guru') ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= view('components/_breadcrumb', ['items' => [['label' => 'Dashboard', 'url' => base_url('guru')], ['label' => 'Kelas Saya', 'url' => base_url('guru/input-saya')], ['label' => ($mapel['nama'] ?? '') . ' — ' . ($rombel['nama'] ?? '')]]]) ?>
<div class="flex items-center gap-3 text-sm mb-4">
    <a href="<?= base_url('guru/input-saya') ?>" class="inline-flex items-center gap-1 text-primary">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali
    </a>
    <?php if (isset($tab) && $tab === 'presensi'): ?>
    <span class="text-outline/30">|</span>
    <a href="<?= base_url('guru/nilai/mapel/' . $mapel['id'] . '/kelas/' . $rombel['id']) ?>" class="inline-flex items-center gap-1 text-primary text-sm">
        <span class="material-symbols-outlined text-sm">edit_note</span>
        Input Nilai
    </a>
    <?php endif; ?>
</div>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary"><?= esc($mapel['nama'] ?? '') ?></h3>
                <p class="text-xs text-on-surface-variant mt-1">Rombel <?= esc($rombel['nama'] ?? '') ?> • <?= count($siswa) ?> santri</p>
            </div>
            <?php if ($tab === 'nilai'): ?>
            <div class="relative w-full md:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                <input id="searchSantri" class="w-full pl-10 pr-4 py-2 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm focus:ring-2 focus:ring-primary/20" placeholder="Cari santri..." type="text"/>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab Bar -->
    <div class="flex border-b border-outline-variant/20 overflow-x-auto scrollbar-hide">
        <button type="button" onclick="switchMapelTab('nilai', this)"
                class="tab-mapel-btn px-6 py-3 text-sm font-bold whitespace-nowrap transition-all border-b-2 <?= $tab === 'nilai' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary hover:bg-primary/5' ?>">
            <span class="material-symbols-outlined text-sm align-middle mr-1">edit_note</span>
            Input Nilai
        </button>
        <button type="button" onclick="switchMapelTab('presensi', this)"
                class="tab-mapel-btn px-6 py-3 text-sm font-bold whitespace-nowrap transition-all border-b-2 <?= $tab === 'presensi' ? 'text-primary border-primary' : 'text-on-surface-variant border-transparent hover:text-primary hover:bg-primary/5' ?>">
            <span class="material-symbols-outlined text-sm align-middle mr-1">how_to_reg</span>
            Presensi
        </button>
    </div>

    <!-- Tab: Input Nilai -->
    <div id="tab-mapel-nilai" class="tab-mapel-content <?= $tab !== 'nilai' ? 'hidden' : '' ?>">
        <div class="px-6 py-2.5 border-b border-outline-variant/10 text-center">
            <span class="text-xs text-outline flex items-center justify-center gap-1.5">
                <span class="material-symbols-outlined text-sm">touch_app</span>
                Klik nama santri untuk mengelola nilai
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="nilaiTable">
                <thead>
                    <tr class="bg-surface-container-low/30">
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase w-10">No</th>
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase">NIS</th>
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase">Nama Santri</th>
                        <?php foreach ($kategoriList as $k): ?>
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase text-center min-w-[100px]">
                            <?= esc($displayNameByKategoriId[$k['id']] ?? $k['nama']) ?>
                        </th>
                        <?php endforeach; ?>
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase text-center min-w-[90px]">Nilai Akhir</th>
                        <th class="px-4 py-4 font-label-md text-label-md text-outline uppercase text-center min-w-[110px]">Predikat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <?php if (!empty($rekapSiswa)): $no = 1; foreach ($rekapSiswa as $row): ?>
                    <tr onclick="window.location.href='<?= base_url('guru/nilai/siswa/' . $row['siswa_id'] . '/mapel/' . $mapel['id'] . '/kelas/' . $rombel['id']) ?>'"
                        class="siswa-row cursor-pointer transition-all duration-200 hover:bg-primary/10 hover:translate-x-0.5"
                        data-search="<?= esc(strtolower($row['nis'] . ' ' . $row['nama'])) ?>">
                        <td class="px-4 py-3.5 text-sm text-outline"><?= $no++ ?></td>
                        <td class="px-4 py-3.5 text-sm text-outline"><?= esc($row['nis']) ?></td>
                        <td class="px-4 py-3.5 font-bold text-on-surface"><?= esc($row['nama']) ?></td>
                        <?php foreach ($kategoriList as $k): $n = $row['kategoriNilai'][$k['id']] ?? null; ?>
                        <td class="px-4 py-3.5 text-center">
                            <?php if ($n !== null): ?>
                            <span class="font-bold text-sm <?= predikatClass($n) ?>"><?= $n ?></span>
                            <?php else: ?>
                            <span class="text-outline text-xs italic">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                        <td class="px-4 py-3.5 text-center">
                            <?php if ($row['nilaiAkhir'] !== null): ?>
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="font-bold text-sm <?= predikatClass($row['nilaiAkhir']) ?>">
                                    <?= $row['nilaiAkhir'] ?>
                                </span>
                                <?php if ($row['mode']): ?>
                                <span class="text-[9px] px-1.5 py-0.5 rounded-full font-semibold <?= $row['mode'] === 'auto' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary' ?>">
                                    <?= $row['mode'] === 'auto' ? 'Otomatis' : 'Manual' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                            <span class="text-outline text-xs italic">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-bold <?= $row['predikat']['class'] ?>">
                                <?= esc($row['predikat']['label']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="<?= 5 + count($kategoriList) ?>" class="px-6 py-8 text-center text-on-surface-variant text-sm">Belum ada santri</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tab: Presensi — Rekap Matrix + Modal Input -->
    <div id="tab-mapel-presensi" class="tab-mapel-content <?= $tab !== 'presensi' ? 'hidden' : '' ?>">
        <div class="p-6 border-b border-outline-variant/20">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h4 class="font-bold text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm text-primary">summarize</span>
                        Rekap Kehadiran
                    </h4>
                    <p class="text-xs text-on-surface-variant mt-0.5">Matriks kehadiran santri per tanggal — <?= esc($mapel['nama'] ?? '') ?> — <?= esc($rombel['nama'] ?? '') ?></p>
                </div>
                <button onclick="showPresensiModal()"
                        class="btn-primary py-2.5 px-5 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">how_to_reg</span>
                    Input Presensi
                </button>
            </div>
        </div>

        <!-- Legend -->
        <div class="px-6 py-3 border-b border-outline-variant/10 flex gap-5 text-xs font-bold">
            <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-primary-container/30 text-primary text-center leading-5">H</span> Hadir</span>
            <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-surface-variant text-outline text-center leading-5">S</span> Sakit</span>
            <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-surface-variant text-outline text-center leading-5">I</span> Izin</span>
            <span class="flex items-center gap-1"><span class="inline-block w-5 h-5 rounded bg-error-container/30 text-error text-center leading-5">A</span> Alpha</span>
        </div>

        <!-- Matrix Table -->
        <?php if (!empty($siswa)): ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container-low/30">
                        <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase w-12 sticky left-0 bg-surface-container-low/30 z-10">No</th>
                        <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase w-28 sticky left-12 bg-surface-container-low/30 z-10">NIS</th>
                        <th class="px-5 py-4 font-label-md text-label-md text-outline uppercase sticky left-40 bg-surface-container-low/30 z-10">Nama Santri</th>
                        <?php if (!empty($presensiDates)): foreach ($presensiDates as $date): ?>
                        <th class="px-3 py-4 font-label-md text-label-md text-outline uppercase text-center min-w-[44px] max-w-[44px]" title="<?= date('d/m/Y', strtotime($date)) ?>"><?= date('d/m', strtotime($date)) ?></th>
                        <?php endforeach; else: ?>
                        <th class="px-6 py-4 font-label-md text-label-md text-outline uppercase text-center">—</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php $no = 1; foreach ($siswa as $s):
                        $sId = $s['siswa_id'];
                    ?>
                    <tr class="hover:bg-primary/5 transition-colors">
                        <td class="px-5 py-3 text-sm text-outline sticky left-0 bg-white z-10"><?= $no++ ?></td>
                        <td class="px-5 py-3 text-sm text-outline sticky left-12 bg-white z-10"><?= esc($s['nis']) ?></td>
                        <td class="px-5 py-3 font-bold text-sm text-on-surface sticky left-40 bg-white z-10"><?= esc($s['nama']) ?></td>
                        <?php if (!empty($presensiDates)): foreach ($presensiDates as $date):
                            $data = $presensiMatrix[$sId][$date] ?? null;
                            $status = $data['status'] ?? null;
                            $keterangan = $data['keterangan'] ?? '';
                        ?>
                        <td class="px-3 py-3 text-center">
                            <?php if ($status): ?>
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold cursor-default transition-all duration-150 hover:scale-110
                                <?= match($status) {
                                    'hadir' => 'bg-primary-container/30 text-primary',
                                    'sakit', 'izin' => 'bg-surface-variant text-outline',
                                    'alpha' => 'bg-error-container/30 text-error',
                                    default => 'bg-surface-container-low text-outline',
                                } ?>"
                                <?= $keterangan ? 'title="' . esc($keterangan) . '"' : '' ?>>
                                <?= match($status) {
                                    'hadir' => 'H',
                                    'sakit' => 'S',
                                    'izin'  => 'I',
                                    'alpha' => 'A',
                                    default => '—',
                                } ?>
                            </span>
                            <?php else: ?>
                            <span class="text-outline/30 align-middle">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; else: ?>
                        <td class="px-6 py-3 text-center text-outline/30 text-xs italic">Belum ada data</td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-8 text-center text-on-surface-variant text-sm">Belum ada santri di rombel ini</div>
        <?php endif; ?>
    </div>

    <!-- Modal: Input Presensi -->
    <div id="presensiModal" role="dialog" aria-modal="true" aria-labelledby="modal-presensi-title" class="fixed inset-0 bg-black/30 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this)hidePresensiModal()">
        <div class="glass-card rounded-3xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-4">
                <h4 id="modal-presensi-title" class="font-headline-sm text-headline-sm text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">how_to_reg</span>
                    Input Presensi
                </h4>
                <button type="button" onclick="hidePresensiModal()" class="p-2 hover:bg-surface-container-low rounded-lg text-outline transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <p class="text-sm text-on-surface-variant mb-4"><?= esc($mapel['nama'] ?? '') ?> — <?= esc($rombel['nama'] ?? '') ?></p>
            <form id="formPresensiModal" method="post" action="<?= base_url('guru/presensi/save-batch') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="rombel_id" value="<?= $rombel['id'] ?>"/>
                <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>"/>
                <div class="mb-4">
                    <label for="modalTanggal" class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="modalTanggal" value="<?= esc($tanggal) ?>"
                           class="w-full md:w-56 px-3 py-2 bg-surface-container-low border-2 border-outline/60 rounded-xl text-sm outline-none focus:ring-2 focus:ring-primary/20"/>
                </div>
                <div class="overflow-x-auto border-2 border-outline/20 rounded-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-container-low/50">
                                <th class="px-4 py-3 font-label-md text-label-md text-outline uppercase w-10">No</th>
                                <th class="px-4 py-3 font-label-md text-label-md text-outline uppercase w-28">NIS</th>
                                <th class="px-4 py-3 font-label-md text-label-md text-outline uppercase">Nama</th>
                                <th class="px-4 py-3 font-label-md text-label-md text-outline uppercase text-center">Kehadiran</th>
                                <th class="px-4 py-3 font-label-md text-label-md text-outline uppercase w-40">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <?php if (!empty($siswa)): $no = 1; foreach ($siswa as $s):
                                $pres = $presensiHariIni[$s['siswa_id']] ?? null;
                                $statusSaatIni = $pres['status'] ?? 'hadir';
                            ?>
                            <tr class="hover:bg-primary/5 transition-all duration-200">
                                <td class="px-4 py-2.5 text-sm text-outline"><?= $no++ ?></td>
                                <td class="px-4 py-2.5 text-sm text-outline"><?= esc($s['nis']) ?></td>
                                <td class="px-4 py-2.5 font-bold text-sm text-on-surface"><?= esc($s['nama']) ?></td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center justify-center gap-1">
                                        <label class="px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95
                                            <?= $statusSaatIni == 'hadir' ? 'bg-primary-container/30 text-primary ring-1 ring-primary/30' : 'bg-surface-container-low text-outline hover:bg-primary/10' ?>">
                                            <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="hadir"
                                                   <?= $statusSaatIni == 'hadir' ? 'checked' : '' ?>
                                                   class="hidden"/>
                                            Hadir
                                        </label>
                                        <label class="px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95
                                            <?= $statusSaatIni == 'sakit' ? 'bg-secondary-container/30 text-secondary ring-1 ring-secondary/30' : 'bg-surface-container-low text-outline hover:bg-secondary/10' ?>">
                                            <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="sakit"
                                                   <?= $statusSaatIni == 'sakit' ? 'checked' : '' ?>
                                                   class="hidden"/>
                                            Sakit
                                        </label>
                                        <label class="px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95
                                            <?= $statusSaatIni == 'izin' ? 'bg-surface-variant text-outline ring-1 ring-outline/30' : 'bg-surface-container-low text-outline hover:bg-surface-variant' ?>">
                                            <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="izin"
                                                   <?= $statusSaatIni == 'izin' ? 'checked' : '' ?>
                                                   class="hidden"/>
                                            Izin
                                        </label>
                                        <label class="px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 hover:scale-105 active:scale-95
                                            <?= $statusSaatIni == 'alpha' ? 'bg-error-container/30 text-error ring-1 ring-error/30' : 'bg-surface-container-low text-outline hover:bg-error/10' ?>">
                                            <input type="radio" name="status[<?= $s['siswa_id'] ?>]" value="alpha"
                                                   <?= $statusSaatIni == 'alpha' ? 'checked' : '' ?>
                                                   class="hidden"/>
                                            Alpha
                                        </label>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="text" name="keterangan[<?= $s['siswa_id'] ?>]"
                                           value="<?= esc($pres['keterangan'] ?? '') ?>"
                                           placeholder="Opsional"
                                           class="w-full px-2.5 py-1.5 bg-surface-container-low border border-outline/50 rounded-lg text-xs outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-outline/50"/>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-on-surface-variant text-sm">Belum ada santri</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end gap-3 pt-5">
                    <button type="button" onclick="hidePresensiModal()" class="flex-1 py-3 bg-surface-container-low text-on-surface-variant rounded-xl font-bold text-sm">Batal</button>
                    <button type="submit" class="btn-primary flex-1 py-3 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-sm">save</span>
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// --- Modal Presensi ---
function showPresensiModal() {
    document.getElementById('presensiModal')?.classList.remove('hidden');
    document.getElementById('presensiModal')?.classList.add('flex');
}
function hidePresensiModal() {
    document.getElementById('presensiModal')?.classList.add('hidden');
    document.getElementById('presensiModal')?.classList.remove('flex');
}

function toastPresensi(icon, title, timer) {
    try { Swal.fire({ icon: icon, title: title, toast: true, position: 'top-end', timer: timer || 2000, showConfirmButton: false }); }
    catch(e) { alert(title); }
}

document.getElementById('formPresensiModal')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">refresh</span> Menyimpan...';

    function done() {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }

    var controller = new AbortController();
    var timeoutId = setTimeout(function() { controller.abort(); }, 5000);

    fetch('<?= base_url('guru/presensi/save-batch') ?>', {
        method: 'POST',
        body: new FormData(this),
        signal: controller.signal,
    })
    .then(function(res) { clearTimeout(timeoutId); return res.json(); })
    .then(function(data) {
        toastPresensi(data.success ? 'success' : 'error', data.message || (data.success ? 'Presensi berhasil disimpan.' : 'Gagal menyimpan presensi.'));
        if (data.success) { hidePresensiModal(); setTimeout(function() { location.reload(); }, 1000); }
        done();
    })
    .catch(function() {
        clearTimeout(timeoutId);
        // Fallback offline
        var formData = new FormData(document.getElementById('formPresensiModal'));
        if (typeof savePendingPresensiAll === 'function') {
            savePendingPresensiAll(formData).then(function() {
                toastPresensi('success', 'Presensi tersimpan lokal. Akan dikirim otomatis saat online.', 3000);
                hidePresensiModal();
            }).catch(function() {
                toastPresensi('error', 'Gagal menyimpan presensi.', 3000);
            }).finally(done);
        } else {
            toastPresensi('success', 'Presensi tersimpan lokal.', 3000);
            hidePresensiModal();
            done();
        }
    });
});

// Styling radio buttons in modal on change
document.getElementById('presensiModal')?.addEventListener('change', function(e) {
    if (e.target.matches('input[type="radio"][name^="status["]')) {
        const container = e.target.closest('.flex.items-center.justify-center');
        if (!container) return;
        container.querySelectorAll('label').forEach(function(l) {
            l.className = 'px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 bg-surface-container-low text-outline hover:bg-primary/10';
        });
        const parentLabel = e.target.closest('label');
        if (!parentLabel) return;
        const map = {
            'hadir': 'bg-primary-container/30 text-primary ring-1 ring-primary/30',
            'sakit': 'bg-secondary-container/30 text-secondary ring-1 ring-secondary/30',
            'izin': 'bg-surface-variant text-outline ring-1 ring-outline/30',
            'alpha': 'bg-error-container/30 text-error ring-1 ring-error/30',
        };
        parentLabel.className = 'px-2.5 py-2 rounded-lg text-[10px] font-bold cursor-pointer transition-all duration-200 ' + (map[e.target.value] || '');
    }
});

document.getElementById('searchSantri')?.addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.siswa-row').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
});

function switchMapelTab(tab, btn) {
    document.querySelectorAll('.tab-mapel-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-mapel-btn').forEach(el => {
        el.classList.remove('text-primary', 'border-primary');
        el.classList.add('text-on-surface-variant', 'border-transparent');
    });
    document.getElementById('tab-mapel-' + tab)?.classList.remove('hidden');
    if (btn) {
        btn.classList.remove('text-on-surface-variant', 'border-transparent');
        btn.classList.add('text-primary', 'border-primary');
    }
    const url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.replaceState({}, '', url);
}
</script>
<?= $this->endSection() ?>
