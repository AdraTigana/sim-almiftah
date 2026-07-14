<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class TimeHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!function_exists('timeAgo')) {
            require_once APPPATH . 'Helpers/time_helper.php';
        }
    }

    public function testTimeAgoNull(): void
    {
        $this->assertSame('', timeAgo(null));
    }

    public function testTimeAgoEmpty(): void
    {
        $this->assertSame('', timeAgo(''));
    }

    public function testTimeAgoJustNow(): void
    {
        $this->assertSame('Baru saja', timeAgo(date('Y-m-d H:i:s')));
    }

    public function testTimeAgoMinutes(): void
    {
        $past = date('Y-m-d H:i:s', time() - 120);
        $this->assertSame('2m lalu', timeAgo($past));
    }

    public function testTimeAgoHours(): void
    {
        $past = date('Y-m-d H:i:s', time() - 7200);
        $this->assertSame('2j lalu', timeAgo($past));
    }

    public function testTimeAgoDays(): void
    {
        $past = date('Y-m-d H:i:s', time() - 172800);
        $this->assertSame(date('d/m/Y', strtotime($past)), timeAgo($past));
    }
}
