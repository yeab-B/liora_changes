<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    /**
     * Forget resolved auth guards before every simulated HTTP call.
     *
     * In real deployments each HTTP request boots a brand new application
     * container, so Laravel's `AuthManager::guard()` / `RequestGuard::user()`
     * memoization never leaks between requests. In PHPUnit, however, a
     * single test method that fires multiple `postJson()`/`getJson()` calls
     * against different Bearer tokens (or after revoking a token) reuses
     * the SAME booted `$this->app`, so the sanctum guard instance — and the
     * user it resolved on the *first* call — stays cached in memory across
     * every subsequent call in that test. That silently authenticates the
     * wrong user (or a since-revoked token) purely as a testing artifact,
     * not a real security issue. Resetting guards before each simulated
     * request makes tests behave like independent real requests.
     */
    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        Auth::forgetGuards();

        return parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);
    }
}
