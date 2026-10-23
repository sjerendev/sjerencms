<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\RichEditor;
use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;

class ManageSettings extends SettingsPage
{
	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
	protected static ?string $navigationLabel = 'Settings';
	protected static ?string $title = 'Studio Settings';
	protected static ?int $navigationSort = 100;
	protected static string $settings = GeneralSettings::class;

	public static function getNavigationGroup(): ?string
	{
		return null;
	}

	public function form(Schema $schema): Schema
	{
		return $schema->components([
			Section::make('Basic Information')
				->schema([
					TextInput::make('site_name')
						->required(),
					TextInput::make('tagline')
						->required(),
					TextInput::make('email')
						->email(),
					TextInput::make('phone')
						->tel(),
					Textarea::make('address')
				]),

			Section::make('Branding')
				->schema([
					FileUpload::make('logo')
						->image()
						->disk('public')
						->visibility('public')
						->directory('branding'),
				]),

			Section::make('Social Media')
				->schema([
					TextInput::make('facebook_url')
						->url()
						->prefix('https://')
						->placeholder('facebook.com/your-page'),
					TextInput::make('instagram_url')
						->url()
						->prefix('https://')
						->placeholder('instagram.com/your-handle'),
					TextInput::make('linkedin_url')
						->url()
						->prefix('https://')
						->placeholder('linkedin.com/company/your-company'),
					TextInput::make('youtube_url')
						->url()
						->prefix('https://')
						->placeholder('youtube.com/@your-channel'),
					TextInput::make('bandcamp_url')
						->url()
						->prefix('https://')
						->placeholder('your-name.bandcamp.com'),
				]),
			Section::make('Navigation Settings')
				->schema([
					Select::make('navigation_type')
						->options([
							'normal' => 'Normal Menu (Hamburger on Mobile)',
							'hamburger' => 'Always Hamburger Menu',
						])
						->required()
						->live(),
					Grid::make(2)
						->schema([
							Toggle::make('show_contact_in_menu')
								->visible(fn(Get $get) => $get('navigation_type') === 'hamburger'),
							Toggle::make('show_social_in_menu')
								->visible(fn(Get $get) => $get('navigation_type') === 'hamburger'),
						]),
					RichEditor::make('menu_contact_info')
						->visible(
							fn(Get $get) =>
							$get('navigation_type') === 'hamburger' &&
								$get('show_contact_in_menu')
						)
						->columnSpanFull(),
				]),

			Section::make('Shop')
				->schema([
					TextInput::make('download_limit')
						->integer()
						->minValue(1)
						->required()
						->helperText('Downloads per purchase'),
					TextInput::make('login_link_minutes')
						->integer()
						->minValue(1)
						->required()
						->helperText('Minutes a login link is valid'),
				]),

			Section::make('Custom Code')
				->description('Add custom code snippets that will be inserted in the head tag and footer of your website')
				->schema([
					Textarea::make('head_code_snippets')
						->label('Head Code Snippets')
						->helperText('This code will be inserted in the <head> tag (e.g. meta tags, favicon links, analytics)')
						->rows(4),
					Textarea::make('footer_code_snippets')
						->label('Footer Code Snippets')
						->helperText('This code will be inserted just before the closing </body> tag (e.g. analytics, chat widgets)')
						->rows(4),
				]),

		]);
	}

	protected function afterSave(): void
	{
		Notification::make()
			->title('Settings saved successfully')
			->success()
			->send();
	}
}
