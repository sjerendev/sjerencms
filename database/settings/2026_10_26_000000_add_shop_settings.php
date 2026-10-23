<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.download_limit', 5);
        $this->migrator->add('general.login_link_minutes', 15);
    }
};
