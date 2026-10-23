<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Refuse to run before RefreshDatabase gets a chance to wipe a real
     * database. A cached config ignores phpunit.xml, and a DB_URL overrides
     * the configured driver, so ask the resolved connection, not the config.
     * Resolving it does not open a PDO connection yet.
     */
    protected function setUpTraits()
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $database = $connection->getDatabaseName();

        if ($driver !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                "Tests must run on sqlite :memory:, got [{$driver}] [{$database}]. Run `php artisan config:clear` and unset DB_URL."
            );
        }

        return parent::setUpTraits();
    }
}
