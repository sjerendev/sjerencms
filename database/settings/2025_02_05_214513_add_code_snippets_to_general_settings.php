<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.head_code_snippets', '');
        $this->migrator->add('general.footer_code_snippets', '');
    }
};
