<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        try {
            $this->migrator->delete('general.favicon');
        } catch (\Exception $e) {
            // Setting doesn't exist, that's fine
        }
    }
};
