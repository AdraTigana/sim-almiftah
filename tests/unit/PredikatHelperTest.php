<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PredikatHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('predikatNilai')) {
            require_once APPPATH . 'Helpers/predikat_helper.php';
        }
    }

    // --- predikatNilai ---

    public function testPredikatNilaiNullReturnsDash(): void
    {
        $this->assertSame('—', predikatNilai(null));
    }

    public function testPredikatNilaiEmptyReturnsDash(): void
    {
        $this->assertSame('—', predikatNilai('—'));
    }

    public function testPredikatNilai85(): void
    {
        $this->assertSame('A', predikatNilai(85));
    }

    public function testPredikatNilai84(): void
    {
        $this->assertSame('B', predikatNilai(84));
    }

    public function testPredikatNilai70(): void
    {
        $this->assertSame('B', predikatNilai(70));
    }

    public function testPredikatNilai69(): void
    {
        $this->assertSame('C', predikatNilai(69));
    }

    public function testPredikatNilai55(): void
    {
        $this->assertSame('C', predikatNilai(55));
    }

    public function testPredikatNilai54(): void
    {
        $this->assertSame('D', predikatNilai(54));
    }

    public function testPredikatNilai40(): void
    {
        $this->assertSame('D', predikatNilai(40));
    }

    public function testPredikatNilai39(): void
    {
        $this->assertSame('E', predikatNilai(39));
    }

    public function testPredikatNilai1(): void
    {
        $this->assertSame('E', predikatNilai(1));
    }

    public function testPredikatNilai0(): void
    {
        $this->assertSame('—', predikatNilai(0));
    }

    // --- predikatLabel ---

    public function testPredikatLabelA(): void
    {
        $this->assertSame('A (Mumtaz)', predikatLabel(90));
    }

    public function testPredikatLabelB(): void
    {
        $this->assertSame('B (Jayyid)', predikatLabel(75));
    }

    public function testPredikatLabelC(): void
    {
        $this->assertSame('C (Maqbul)', predikatLabel(60));
    }

    public function testPredikatLabelD(): void
    {
        $this->assertSame('D (Naqis)', predikatLabel(45));
    }

    public function testPredikatLabelE(): void
    {
        $this->assertSame('E (Dhaif)', predikatLabel(20));
    }

    public function testPredikatLabelNull(): void
    {
        $this->assertSame('—', predikatLabel(null));
    }

    // --- predikatClass ---

    public function testPredikatClassA(): void
    {
        $this->assertSame('text-primary', predikatClass(85));
    }

    public function testPredikatClassB(): void
    {
        $this->assertSame('text-secondary', predikatClass(70));
    }

    public function testPredikatClassC(): void
    {
        $this->assertSame('text-tertiary', predikatClass(55));
    }

    public function testPredikatClassD(): void
    {
        $this->assertSame('text-error', predikatClass(40));
    }

    public function testPredikatClassE(): void
    {
        $this->assertSame('text-error', predikatClass(30));
    }

    public function testPredikatClassNull(): void
    {
        $this->assertSame('text-outline', predikatClass(null));
    }

    // --- isTuntas ---

    public function testIsTuntas70(): void
    {
        $this->assertTrue(isTuntas(70));
    }

    public function testIsTuntas85(): void
    {
        $this->assertTrue(isTuntas(85));
    }

    public function testIsTuntas69(): void
    {
        $this->assertFalse(isTuntas(69));
    }

    public function testIsTuntasNull(): void
    {
        $this->assertFalse(isTuntas(null));
    }

    public function testIsTuntas0(): void
    {
        $this->assertFalse(isTuntas(0));
    }

    // --- isMapelTasmi ---

    public function testIsMapelTasmiId1(): void
    {
        $this->assertTrue(isMapelTasmi(1));
    }

    public function testIsMapelTasmiId9(): void
    {
        $this->assertTrue(isMapelTasmi(9));
    }

    public function testIsMapelTasmiId2(): void
    {
        $this->assertFalse(isMapelTasmi(2));
    }

    public function testIsMapelTasmiId8(): void
    {
        $this->assertFalse(isMapelTasmi(8));
    }

    public function testIsMapelTasmiString1(): void
    {
        $this->assertTrue(isMapelTasmi('1'));
    }

    // --- kategoriDisplayName ---

    public function testKategoriDisplayNameNonTasmiUrutan1(): void
    {
        $kategori = ['id' => 1, 'nama' => 'Harian', 'urutan' => 1];
        $this->assertSame('Nilai Harian', kategoriDisplayName($kategori, 2));
    }

    public function testKategoriDisplayNameNonTasmiUrutan2(): void
    {
        $kategori = ['id' => 2, 'nama' => 'Tugas', 'urutan' => 2];
        $this->assertSame('Nilai Tugas', kategoriDisplayName($kategori, 2));
    }

    public function testKategoriDisplayNameNonTasmiUrutan3(): void
    {
        $kategori = ['id' => 3, 'nama' => 'Ujian', 'urutan' => 3];
        $this->assertSame('Nilai Ujian', kategoriDisplayName($kategori, 2));
    }

    public function testKategoriDisplayNameNonTasmiUrutan4(): void
    {
        $kategori = ['id' => 4, 'nama' => 'Lainnya', 'urutan' => 4];
        $this->assertSame('Lainnya', kategoriDisplayName($kategori, 2));
    }

    public function testKategoriDisplayNameTasmi1Urutan5(): void
    {
        $kategori = ['id' => 5, 'nama' => 'Tasmi 1', 'urutan' => 5];
        $this->assertSame('Nilai Ujian', kategoriDisplayName($kategori, 1));
    }

    public function testKategoriDisplayNameTasmi1Urutan1(): void
    {
        $kategori = ['id' => 1, 'nama' => 'Tasmi 1', 'urutan' => 1];
        $this->assertSame('Tasmi 1', kategoriDisplayName($kategori, 1));
    }

    public function testKategoriDisplayNameTasmi9Urutan3(): void
    {
        $kategori = ['id' => 3, 'nama' => 'Ujian Ghorib', 'urutan' => 3];
        $this->assertSame('Nilai Ujian', kategoriDisplayName($kategori, 9));
    }

    public function testKategoriDisplayNameTasmi9Urutan2(): void
    {
        $kategori = ['id' => 2, 'nama' => 'Tasmi 9-2', 'urutan' => 2];
        $this->assertSame('Tasmi 9-2', kategoriDisplayName($kategori, 9));
    }

    // --- kategoriDisplayNameMap ---

    public function testKategoriDisplayNameMap(): void
    {
        $kategoriList = [
            ['id' => 1, 'nama' => 'Harian', 'urutan' => 1],
            ['id' => 2, 'nama' => 'Tugas', 'urutan' => 2],
            ['id' => 3, 'nama' => 'Ujian', 'urutan' => 3],
        ];
        $expected = [
            1 => 'Nilai Harian',
            2 => 'Nilai Tugas',
            3 => 'Nilai Ujian',
        ];
        $this->assertSame($expected, kategoriDisplayNameMap($kategoriList, 2));
    }
}
