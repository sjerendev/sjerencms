<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Refuse to run before RefreshDatabase gets a chance to wipe a real
     * database. A cached config ignores phpunit.xml and points tests at the
     * local MySQL database.
     */
    protected function setUpTraits()
    {
        $connection = config('database.default');

        if ($connection !== 'sqlite' || config("database.connections.{$connection}.database") !== ':memory:') {
            throw new RuntimeException(
                "Tests must run on sqlite :memory:, got [{$connection}]. Run `php artisan config:clear`."
            );
        }

        return parent::setUpTraits();
    }
}
