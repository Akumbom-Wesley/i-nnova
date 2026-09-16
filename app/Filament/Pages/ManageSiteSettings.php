<?php

namespace App\Filament\Pages;

use App\Filament\Support\LocaleTabs;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Site Settings is a single row, so it gets a page rather than a resource.
 * There is no list, no create and no way to end up with two of them.
 */
class ManageSiteSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected string $view = 'filament.pages.manage-site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Site settings';

    protected static ?string $navigationLabel = 'Site settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::instance()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->record(SiteSetting::instance())
            ->statePath('data')
            ->components([
                Section::make('Hero')
                    ->description('The first thing anyone reads on the home page.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("hero_heading.{$locale}")
                                ->label('Heading')
                                ->maxLength(160)
                                ->columnSpanFull(),

                            Textarea::make("hero_subheading.{$locale}")
                                ->label('Subheading')
                                ->rows(3)
                                ->columnSpanFull(),

                            TextInput::make("hero_cta_label.{$locale}")
                                ->label('Button label')
                                ->maxLength(60),
                        ]),

                        TextInput::make('hero_cta_url')
                            ->label('Button link')
                            ->maxLength(255),
                    ]),

                Section::make('About')
                    ->description('The story on the About page. Values and how we work are edited as their own lists.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("about_heading.{$locale}")
                                ->label('Heading')
                                ->maxLength(160)
                                ->columnSpanFull(),

                            RichEditor::make("about_story.{$locale}")
                                ->label('Story')
                                ->columnSpanFull(),
                        ]),
                    ]),

                Section::make('Contact')
                    ->description('Leave a field empty rather than filling it with a placeholder. Only real, verified details belong here.')
                    ->schema([
                        TextInput::make('contact_email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('contact_phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(40),

                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(40)
                            ->helperText('International format, for example +237670000000.'),

                        TextInput::make('kickstarter_url')
                            ->label('Kickstarter platform')
                            ->url()
                            ->maxLength(255),

                        LocaleTabs::make(fn (string $locale) => [
                            Textarea::make("address.{$locale}")
                                ->label('Address')
                                ->rows(3)
                                ->columnSpanFull(),
                        ], 'Address'),
                    ])
                    ->columns(2),

                Section::make('Social links')
                    ->schema([
                        TextInput::make('socials.linkedin')->label('LinkedIn')->url(),
                        TextInput::make('socials.facebook')->label('Facebook')->url(),
                        TextInput::make('socials.instagram')->label('Instagram')->url(),
                        TextInput::make('socials.x')->label('X')->url(),
                        TextInput::make('socials.youtube')->label('YouTube')->url(),
                        TextInput::make('socials.github')->label('GitHub')->url(),
                    ])
                    ->columns(2),

                Section::make('SEO defaults')
                    ->description('Used on any page that does not set its own.')
                    ->schema([
                        LocaleTabs::make(fn (string $locale) => [
                            TextInput::make("seo_title.{$locale}")
                                ->label('Title')
                                ->maxLength(70)
                                ->columnSpanFull(),

                            Textarea::make("seo_description.{$locale}")
                                ->label('Description')
                                ->rows(3)
                                ->maxLength(180)
                                ->columnSpanFull(),
                        ]),

                        SpatieMediaLibraryFileUpload::make('og_image')
                            ->collection('og_image')
                            ->image()
                            ->imageEditor()
                            ->helperText('Shown when a link to the site is shared.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill($this->form->getState());
        $settings->save();

        $this->form->model($settings)->saveRelationships();

        SiteSetting::forgetInstance();

        Notification::make()
            ->success()
            ->title('Site settings saved')
            ->send();
    }
}
