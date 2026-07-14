<?php $currentMode = $nilaiAkhir['catatan'] ?? 'auto'; ?>

<form method="post" action="<?= base_url('guru/nilai/save-akhir') ?>" id="formNilai-akhir">
    <?= csrf_field() ?>
    <input type="hidden" name="siswa_id" value="<?= $siswa['id'] ?>"/>
    <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>"/>
    <input type="hidden" name="rombel_id" value="<?= $rombel['id'] ?>"/>
    <input type="hidden" name="mode" id="modeAkhir" value="<?= esc($currentMode) ?>"/>

<div class="glass-card rounded-3xl overflow-hidden shadow-sm shadow-primary/5 mb-6">
    <div class="px-6 py-5 border-b border-outline-variant/20">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <h4 class="font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">database</span>
                Rekapitulasi Nilai
            </h4>
            <div class="flex items-center gap-2">
                <button type="button" id="btnSyncAkhir" onclick="manualSync()"
                        class="px-3 py-2 rounded-xl text-xs font-bold border-2 border-outline/30 text-on-surface-variant hover:bg-primary/10 hover:text-primary hover:border-primary/30 transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">sync</span>
                    <span id="syncLabel">Sync Offline Data</span>
                    <span id="syncCountBadge" class="hidden text-[10px] bg-primary/10 text-primary px-1.5 py-0.5 rounded-full font-bold"></span>
                </button>
                <div class="flex gap-2">
                <button type="button" onclick="setModeAkhir('auto')"
                        data-mode="auto"
                        class="mode-akhir-btn px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 <?= $currentMode === 'auto' ? 'bg-primary text-on-primary shadow-sm ring-2 ring-primary/30' : 'bg-surface-container-low text-on-surface-variant hover:bg-primary/10' ?>">
                    <span class="material-symbols-outlined text-sm mode-check-icon <?= $currentMode === 'auto' ? '' : 'hidden' ?>">check</span>
                    Otomatis
                </button>
                <button type="button" onclick="setModeAkhir('manual')"
                        data-mode="manual"
                        class="mode-akhir-btn px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 <?= $currentMode === 'manual' ? 'bg-primary text-on-primary shadow-sm ring-2 ring-primary/30' : 'bg-surface-container-low text-on-surface-variant hover:bg-primary/10' ?>">
                    <span class="material-symbols-outlined text-sm mode-check-icon <?= $currentMode === 'manual' ? '' : 'hidden' ?>">check</span>
                    Manual
                </button>
            </div>
        </div>
    </div>
    <div class="divide-y divide-outline-variant/10">
        <?php foreach ($recapData as $r): ?>
        <div class="px-6 py-4 flex items-center justify-between hover:bg-primary/5 transition-colors">
            <div class="min-w-0 mr-4">
                <p class="font-bold text-sm text-on-surface"><?= esc($r['nama']) ?></p>
                <?php if (!empty($r['detail'])): ?>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1">
                    <?php foreach ($r['detail'] as $d): ?>
                    <span class="text-xs text-outline">
                        <?= htmlspecialchars((string)($d['kriteria'] ?? '')) ?>: <span class="font-semibold text-on-surface-variant"><?= htmlspecialchars((string)($d['nilai'] ?? '')) ?></span>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="text-right shrink-0 ml-4">
                <input type="number" name="kategori_nilai[<?= $r['kategori_id'] ?>]" min="0" max="100"
                       value="<?= $r['nilai'] !== null ? (int)$r['nilai'] : '' ?>"
                       class="kategori-manual-input w-20 px-3 py-2 bg-surface border-2 border-outline/60 rounded-lg text-sm text-center font-bold outline-none focus:ring-2 focus:ring-primary/20 <?= $currentMode !== 'manual' ? 'opacity-50' : '' ?>"
                       <?= $currentMode !== 'manual' ? 'readonly' : '' ?>
                       oninput="hitungNilaiAkhirManual()"/>
            </div>
        </div>
        <?php endforeach; ?>
        <!-- Nilai Akhir -->
        <div class="px-6 py-4 flex items-center justify-between bg-primary/5">
            <div>
                <p class="font-bold text-sm text-primary">Nilai Akhir</p>
                <span id="modeLabel" class="text-[10px] <?= $currentMode === 'auto' ? 'text-primary' : 'text-secondary' ?>"><?= $currentMode === 'auto' ? 'Otomatis' : 'Manual' ?></span>
            </div>
            <div class="text-right shrink-0 ml-4">
                <span id="nilaiAkhirDisplay" class="font-bold text-lg text-primary"><?= $autoGrade ?? '—' ?></span>
                <span class="text-xs text-outline block">/100</span>
            </div>
        </div>
    </div>
</div>

<div class="glass-card rounded-3xl p-6">
    <div class="space-y-5">
        <!-- Predikat -->
        <div>
            <label class="text-xs text-outline uppercase tracking-wider font-semibold block mb-1">Predikat</label>
            <div id="predikatAkhir" class="px-4 py-2 rounded-xl text-sm font-bold inline-block bg-surface-container-low text-on-surface-variant">—</div>
        </div>
    </div>

    <button type="submit" class="btn-primary mt-5 py-2.5 px-6 bg-primary text-on-primary rounded-xl font-bold text-sm shadow-md hover:shadow-lg active:scale-[0.97] transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">save</span>
        Simpan Nilai Akhir
    </button>
</div>

</form>

<script>
function hitungNilaiAkhirManual() {
    const inputs = document.querySelectorAll('.kategori-manual-input');
    let total = 0, count = 0;
    inputs.forEach(inp => {
        const v = parseFloat(inp.value);
        if (!isNaN(v) && v >= 0) {
            total += v;
            count++;
        }
    });
    const rata = count > 0 ? Math.round(total / count) : 0;
    const display = document.getElementById('nilaiAkhirDisplay');
    display.textContent = count > 0 ? rata : '—';
    updatePredikatAkhir(rata, count > 0);
}

function setModeAkhir(mode) {
    document.getElementById('modeAkhir').value = mode;
    document.getElementById('modeLabel').textContent = mode === 'auto' ? 'Otomatis' : 'Manual';
    document.getElementById('modeLabel').className = 'text-[10px] ' + (mode === 'auto' ? 'text-primary' : 'text-secondary');

    document.querySelectorAll('.mode-akhir-btn').forEach(function(btn) {
        btn.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm', 'ring-2', 'ring-primary/30');
        btn.classList.add('bg-surface-container-low', 'text-on-surface-variant');
        var icon = btn.querySelector('.mode-check-icon');
        if (icon) icon.classList.add('hidden');
    });
    var activeBtn = document.querySelector('.mode-akhir-btn[data-mode="' + mode + '"]');
    if (activeBtn) {
        activeBtn.classList.remove('bg-surface-container-low', 'text-on-surface-variant');
        activeBtn.classList.add('bg-primary', 'text-on-primary', 'shadow-sm', 'ring-2', 'ring-primary/30');
        var icon = activeBtn.querySelector('.mode-check-icon');
        if (icon) icon.classList.remove('hidden');
    }

    // Toggle input fields
    document.querySelectorAll('.kategori-manual-input').forEach(inp => {
        if (mode === 'manual') {
            inp.removeAttribute('readonly');
            inp.classList.remove('opacity-50');
        } else {
            inp.setAttribute('readonly', 'true');
            inp.classList.add('opacity-50');
        }
    });

    if (mode === 'auto') {
        // Reset tampilan ke nilai dari DB
        const display = document.getElementById('nilaiAkhirDisplay');
        display.textContent = <?= $autoGrade ?? 0 ?>;
        updatePredikatAkhir(<?= $autoGrade ?? 0 ?>, <?= ($autoGrade !== null) ? 'true' : 'false' ?>);
    } else {
        hitungNilaiAkhirManual();
    }
}

function updatePredikatAkhir(nilai, hasValue) {
    const el = document.getElementById('predikatAkhir');
    let label;
    if (hasValue && nilai >= 85) label = 'A (Mumtaz)';
    else if (hasValue && nilai >= 70) label = 'B (Jayyid)';
    else if (hasValue && nilai >= 55) label = 'C (Maqbul)';
    else if (hasValue && nilai >= 40) label = 'D (Naqis)';
    else if (hasValue && nilai > 0) label = 'E (Dhaif)';
    else label = '—';

    el.textContent = label;
    el.className = hasValue && nilai > 0
        ? 'px-4 py-2 rounded-xl text-sm font-bold bg-primary-container/20 text-primary inline-block'
        : 'px-4 py-2 rounded-xl text-sm font-bold bg-surface-container-low text-on-surface-variant inline-block';
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.kategori-manual-input')) {
        hitungNilaiAkhirManual();
    } else {
        const g = <?= $autoGrade ?? 0 ?>;
        updatePredikatAkhir(g, <?= ($autoGrade !== null) ? 'true' : 'false' ?>);
    }

    if (typeof getPendingSyncCount === 'function') {
        getPendingSyncCount().then(function(count) {
            if (count > 0) {
                var badge = document.getElementById('syncCountBadge');
                if (badge) { badge.textContent = '(' + count + ')'; badge.classList.remove('hidden'); }
            }
        });
    }
});

function manualSync() {
    var btn = document.getElementById('btnSyncAkhir');
    var icon = btn.querySelector('.material-symbols-outlined');
    var label = document.getElementById('syncLabel');

    icon.textContent = 'sync';
    icon.className = 'material-symbols-outlined text-sm animate-spin';
    label.textContent = 'Menyinkronkan...';
    btn.disabled = true;

    if (window.syncPendingData) {
        window.syncPendingData().then(function(success) {
            if (success) {
                icon.textContent = 'check_circle';
                icon.className = 'material-symbols-outlined text-sm';
                label.textContent = 'Tersinkronisasi';
                btn.classList.remove('text-on-surface-variant', 'border-outline-variant/30', 'hover:bg-primary/10', 'hover:text-primary', 'hover:border-primary/30');
                btn.classList.add('text-green-600', 'border-green-300', 'bg-green-50');

                var badge = document.getElementById('syncCountBadge');
                if (badge) { badge.classList.add('hidden'); badge.textContent = ''; }

                setTimeout(function() { location.reload(); }, 1000);
            } else {
                icon.textContent = 'error';
                icon.className = 'material-symbols-outlined text-sm';
                label.textContent = 'Gagal sinkron';
                btn.disabled = false;
                setTimeout(function() {
                    icon.textContent = 'sync';
                    icon.className = 'material-symbols-outlined text-sm';
                    label.textContent = 'Sync Offline Data';
                    btn.classList.remove('text-green-600', 'border-green-300', 'bg-green-50');
                    btn.classList.add('text-on-surface-variant', 'border-outline-variant/30', 'hover:bg-primary/10', 'hover:text-primary', 'hover:border-primary/30');
                }, 3000);
            }
        });
    } else {
        icon.textContent = 'warning';
        icon.className = 'material-symbols-outlined text-sm';
        label.textContent = 'Tidak tersedia';
        btn.disabled = false;
        setTimeout(function() {
            icon.textContent = 'sync';
            icon.className = 'material-symbols-outlined text-sm';
            label.textContent = 'Sync Offline Data';
        }, 3000);
    }
}
</script>
