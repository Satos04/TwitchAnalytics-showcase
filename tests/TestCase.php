<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }
    public function setUp(): void
    {
        parent::setUp();

        // Avoids the "Auth driver [api] not defined" error
        $this->app['config']->set('auth.guards.api', [
            'driver' => 'closure', // or 'token' when using the token driver
            'provider' => 'users',
        ]);
    }
}
