<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateGeneralSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Kaliber Creative Studio');
        $this->migrator->add('general.tagline', 'UI/UX Design & Web Development');
        $this->migrator->add('general.email', '');
        $this->migrator->add('general.phone', '');
        $this->migrator->add('general.address', '');
        $this->migrator->add('general.facebook_url', '');
        $this->migrator->add('general.instagram_url', '');
        $this->migrator->add('general.linkedin_url', '');
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.favicon', '');
    }
}
