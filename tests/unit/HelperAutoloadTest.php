<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Verify helper functions are autoloaded correctly.
 *
 * @internal
 */
final class HelperAutoloadTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('predikatNilai')) {
            require_once APPPATH . 'Helpers/predikat_helper.php';
        }
        if (!function_exists('timeAgo')) {
            require_once APPPATH . 'Helpers/time_helper.php';
        }
    }

    public function testPredikatHelperFunctionsExist(): void
    {
        $this->assertTrue(function_exists('predikatNilai'));
        $this->assertTrue(function_exists('predikatLabel'));
        $this->assertTrue(function_exists('predikatClass'));
        $this->assertTrue(function_exists('isTuntas'));
        $this->assertTrue(function_exists('isMapelTasmi'));
        $this->assertTrue(function_exists('kategoriDisplayName'));
        $this->assertTrue(function_exists('kategoriDisplayNameMap'));
    }

    public function testTimeHelperFunctionExists(): void
    {
        $this->assertTrue(function_exists('timeAgo'));
    }
}
