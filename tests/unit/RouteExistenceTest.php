<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Test key routes respond without errors (GET).
 *
 * @internal
 */
final class RouteExistenceTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        session()->remove('isLoggedIn');
    }

    public function testRouteAdminLoginRedirect(): void
    {
        // Not logged in, should redirect to auth/login
        $result = $this->get('admin');
        $result->assertStatus(302);
    }

    public function testRouteGuruLoginRedirect(): void
    {
        $result = $this->get('guru');
        $result->assertStatus(302);
    }

    public function testRouteWalasLoginRedirect(): void
    {
        $result = $this->get('walas');
        $result->assertStatus(302);
    }
}
