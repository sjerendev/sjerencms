<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;

class ManageSettings extends SettingsPage
{
	protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
	protected static ?string $navigationLabel = 'Settings';
	protected static ?string $title = 'Studio Settings';
	protected static ?int $navigationSort = 100;
	protected static string $settings = GeneralSettings::class;

	public static function getNavigationGroup(): ?string
	{
		return null;
	}

	public function form(Form $form): Form
	{
		return $form->schema([
			Forms\Components\Section::make('Basic Information')
				->schema([
					Forms\Components\TextInput::make('site_name')
						->required(),
					Forms\Components\TextInput::make('tagline')
						->required(),
					Forms\Components\TextInput::make('email')
						->email(),
					Forms\Components\TextInput::make('phone')
						->tel(),
					Forms\Components\Textarea::make('address')
				]),

			Forms\Components\Section::make('Branding')
				->schema([
					Forms\Components\FileUpload::make('logo')
						->image()
						->directory('branding'),
				]),

			Forms\Components\Section::make('Social Media')
				->schema([
					Forms\Components\TextInput::make('facebook_url')
						->url()
						->prefix('https://')
						->placeholder('facebook.com/your-page'),
					Forms\Components\TextInput::make('instagram_url')
						->url()
						->prefix('https://')
						->placeholder('instagram.com/your-handle'),
					Forms\Components\TextInput::make('linkedin_url')
						->url()
						->prefix('https://')
						->placeholder('linkedin.com/company/your-company'),
					Forms\Components\TextInput::make('youtube_url')
						->url()
						->prefix('https://')
						->placeholder('youtube.com/@your-channel'),
				]),
			Forms\Components\Section::make('Navigation Settings')
				->schema([
					Forms\Components\Select::make('navigation_type')
						->options([
							'normal' => 'Normal Menu (Hamburger on Mobile)',
							'hamburger' => 'Always Hamburger Menu',
						])
						->required()
						->live(),
					Forms\Components\Grid::make(2)
						->schema([
							Forms\Components\Toggle::make('show_contact_in_menu')
								->visible(fn(Forms\Get $get) => $get('navigation_type') === 'hamburger'),
							Forms\Components\Toggle::make('show_social_in_menu')
								->visible(fn(Forms\Get $get) => $get('navigation_type') === 'hamburger'),
						]),
					Forms\Components\RichEditor::make('menu_contact_info')
						->visible(
							fn(Forms\Get $get) =>
							$get('navigation_type') === 'hamburger' &&
								$get('show_contact_in_menu')
						)
						->columnSpanFull(),
				]),

			Forms\Components\Section::make('Custom Code')
				->description('Add custom code snippets that will be inserted in the head tag and footer of your website')
				->schema([
					Forms\Components\Textarea::make('head_code_snippets')
						->label('Head Code Snippets')
						->helperText('This code will be inserted in the <head> tag (e.g. meta tags, favicon links, analytics)')
						->rows(4),
					Forms\Components\Textarea::make('footer_code_snippets')
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
