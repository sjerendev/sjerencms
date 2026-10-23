<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Patrik's real contact links, from sjeren-2026 Footer.tsx and Header.tsx.
     */
    public function up(): void
    {
        $this->migrator->add('general.bandcamp_url', 'https://patriksjeren.bandcamp.com/');
        $this->migrator->update('general.email', fn () => 'patrik.sjeren@gmail.com');
        $this->migrator->update('general.linkedin_url', fn () => 'https://www.linkedin.com/in/patrik-sjeren/');
        $this->migrator->update('general.facebook_url', fn () => 'https://www.facebook.com/sjeren');
        $this->migrator->update('general.youtube_url', fn () => 'https://www.youtube.com/@patriksjeren/');
    }
};
