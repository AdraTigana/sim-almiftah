<?php

if (!function_exists('predikatNilai')) {
    function predikatNilai($nilai): string
    {
        if ($nilai === null || $nilai === '—') return '—';
        $n = (int) $nilai;
        if ($n >= 85) return 'A';
        if ($n >= 70) return 'B';
        if ($n >= 55) return 'C';
        if ($n >= 40) return 'D';
        if ($n > 0) return 'E';
        return '—';
    }
}

if (!function_exists('predikatLabel')) {
    function predikatLabel($nilai): string
    {
        $label = match (predikatNilai($nilai)) {
            'A' => 'A (Mumtaz)',
            'B' => 'B (Jayyid)',
            'C' => 'C (Maqbul)',
            'D' => 'D (Naqis)',
            'E' => 'E (Dhaif)',
            default => '—',
        };
        return $label;
    }
}

if (!function_exists('predikatClass')) {
    function predikatClass($nilai): string
    {
        return match (predikatNilai($nilai)) {
            'A' => 'text-primary',
            'B' => 'text-secondary',
            'C' => 'text-tertiary',
            'D' => 'text-error',
            'E' => 'text-error',
            default => 'text-outline',
        };
    }
}

if (!function_exists('isTuntas')) {
    function isTuntas($nilai): bool
    {
        return $nilai !== null && (int) $nilai >= 70;
    }
}

if (!function_exists('isMapelTasmi')) {
    function isMapelTasmi($mapelId): bool
    {
        return in_array((int) $mapelId, [1, 9, 13]);
    }
}

if (!function_exists('kategoriDisplayName')) {
    function kategoriDisplayName(array $kategori, $mapelId): string
    {
        return $kategori['nama'];
    }
}

if (!function_exists('kategoriDisplayNameMap')) {
    function kategoriDisplayNameMap(array $kategoriList, $mapelId): array
    {
        $result = [];
        foreach ($kategoriList as $k) {
            $result[$k['id']] = $k['nama'];
        }
        return $result;
    }
}
