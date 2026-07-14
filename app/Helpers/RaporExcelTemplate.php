<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RaporExcelTemplate
{
    protected string $templatePath;
    protected array $data;

    public function __construct(string $templatePath)
    {
        $this->templatePath = $templatePath;
    }

    public function generate(array $data, string $outputPath = ''): string
    {
        $this->data = $data;

        $spreadsheet = IOFactory::load($this->templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Collect placeholders from all cells
        $placeholders = $this->_buildPlaceholders();

        foreach ($sheet->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $value = $cell->getValue();
                if (is_string($value) && $value !== '') {
                    $newValue = str_replace(
                        array_keys($placeholders),
                        array_values($placeholders),
                        $value
                    );
                    if ($newValue !== $value) {
                        $cell->setValue($newValue);
                    }
                }
            }
        }

        // Output
        if ($outputPath) {
            $writer = new Xlsx($spreadsheet);
            $writer->save($outputPath);
            return $outputPath;
        }

        return $this->stream($spreadsheet);
    }

    protected function _buildPlaceholders(): array
    {
        $d = $this->data;
        $siswa = $d['siswa'] ?? [];
        $rombel = $d['rombel'] ?? [];
        $p = $d['presensi'] ?? [];
        $total = max((int)($p['total'] ?? 1), 1);
        $sakit = (int)($p['sakit'] ?? 0);
        $sakitPct = round($sakit / $total * 100);

        // Flatten mapel data: iterate groups in order, take all mapels
        $mapelFlat = [];
        foreach (($d['mapelGroups'] ?? []) as $group) {
            foreach (($group['mapels'] ?? []) as $m) {
                $mapelFlat[] = $m;
            }
        }

        $map = [];

        // Identity
        $map['[NAMA_SISWA]'] = strtoupper($siswa['nama'] ?? '');
        $map['[KELAS]'] = $rombel['nama'] ?? '';
        $map['[TAHUN_AJAR]'] = $d['tahun_ajar'] ?? '';
        $map['[NIS_SISWA]'] = ($siswa['nis'] ?? '') . ' / ' . ($siswa['nisn'] ?? '—');

        // Footer
        $map['[TANGGAL]'] = date('d F Y');
        $map['[NAMA_WALAS]'] = $d['nama_walas'] ?? '';
        $map['[NUPTK]'] = $d['walas_nuptk'] ?? '................................';

        // Mapel data (template has 11 rows)
        for ($i = 1; $i <= 11; $i++) {
            $m = $mapelFlat[$i - 1] ?? null;
            if ($m) {
                $map["[MAPEL_$i]"] = $m['nama'] ?? '';
                $map["[KKM_$i]"] = (string)($m['kkm'] ?? '70');
                $map["[NILAI_$i]"] = (string)($m['nilai_p'] ?? '—');
                $map["[PREDIKAT_$i]"] = $m['predikat_p'] ?? '—';
                $map["[RERATA_$i]"] = (string)($m['rerata'] ?? '—');
                $map["[RERATA_PRED_$i]"] = $m['rerata_predikat'] ?? '—';
            } else {
                $map["[MAPEL_$i]"] = '';
                $map["[KKM_$i]"] = '';
                $map["[NILAI_$i]"] = '';
                $map["[PREDIKAT_$i]"] = '';
                $map["[RERATA_$i]"] = '';
                $map["[RERATA_PRED_$i]"] = '';
            }
        }

        // Presensi
        $map['[SAKIT]'] = (string)$sakit;
        $map['[SAKIT_PCT]'] = (string)$sakitPct;
        $map['[IZIN]'] = (string)(int)($p['izin'] ?? 0);
        $map['[ALPHA]'] = (string)(int)($p['alpha'] ?? 0);
        $map['[PERTEMUAN]'] = (string)$total;

        // Raisul NIP — template has "NIP: -" at C43
        $raisulNip = $d['raisul_nip'] ?? 'NIP: ................................';

        return $map;
    }

    protected function stream($spreadsheet): never
    {
        $filename = 'rapor_' . url_title($this->data['siswa']['nama'] ?? 'rapor', '-', true) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
