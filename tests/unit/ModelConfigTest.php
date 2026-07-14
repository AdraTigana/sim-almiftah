<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Models\UserModel;
use App\Models\SiswaModel;
use App\Models\PresensiModel;
use App\Models\RombelModel;
use App\Models\MapelModel;

/**
 * Validate model configuration (no DB needed).
 *
 * @internal
 */
final class ModelConfigTest extends CIUnitTestCase
{
    public function testUserModelConfig(): void
    {
        $model = new UserModel();
        $this->assertSame('users', $model->table);
        $this->assertTrue($model->useSoftDeletes);
        $this->assertTrue($model->useTimestamps);
    }

    public function testSiswaModelConfig(): void
    {
        $model = new SiswaModel();
        $this->assertSame('siswa', $model->table);
        $this->assertTrue($model->useSoftDeletes);
        $this->assertTrue($model->useTimestamps);
    }

    public function testPresensiModelConfig(): void
    {
        $model = new PresensiModel();
        $this->assertSame('presensi', $model->table);
        $this->assertFalse($model->useSoftDeletes);
        $this->assertTrue($model->useTimestamps);
    }

    public function testRombelModelConfig(): void
    {
        $model = new RombelModel();
        $this->assertSame('rombel', $model->table);
        $this->assertTrue($model->useTimestamps);
    }

    public function testMapelModelConfig(): void
    {
        $model = new MapelModel();
        $this->assertSame('mapel', $model->table);
        $this->assertFalse($model->useSoftDeletes);
        $this->assertTrue($model->useTimestamps);
    }
}
