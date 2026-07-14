<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Test auth page access (GET only, no CSRF needed).
 *
 * @internal
 */
final class AuthFeatureTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        session()->remove('isLoggedIn');
    }

    public function testLoginPageLoads(): void
    {
        $result = $this->get('auth/login');
        $result->assertStatus(200);
        $result->assertSee('Login');
    }

    public function testLoginPageDoesNotContainError(): void
    {
        $result = $this->get('auth/login');
        $this->assertStringNotContainsString('Terlalu banyak percobaan', $result->getBody());
    }

    public function testLoginPageHasForm(): void
    {
        $result = $this->get('auth/login');
        $this->assertStringContainsString('<form', $result->getBody());
        $this->assertStringContainsString('email', $result->getBody());
        $this->assertStringContainsString('password', $result->getBody());
    }
}
