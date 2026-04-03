<?php

namespace App\Filament\Schemas;

use App\Modules\Websites\Enums\SiteStatus;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class WebsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Website Settings')
                    ->persistTabInQueryString()
                    ->disabled(fn (?Model $record): bool => Filament::getCurrentPanel()?->getId() === 'app' && $record?->status === SiteStatus::Inactive)
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->required()
                                    ->options(fn (): array => Filament::getCurrentPanel()?->getId() === 'admin'
                                        ? SiteStatus::class
                                        : collect(SiteStatus::cases())
                                            ->reject(fn (SiteStatus $status) => $status === SiteStatus::Inactive)
                                            ->mapWithKeys(fn (SiteStatus $status) => [$status->value => $status->getLabel()])
                                            ->toArray()
                                    )
                                    ->default(SiteStatus::Development),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                TextInput::make('default_meta_title')
                                    ->maxLength(255),
                                Textarea::make('default_meta_description')
                                    ->columnSpanFull(),
                                Textarea::make('default_meta_keywords')
                                    ->columnSpanFull(),
                                TextInput::make('canonical_url')
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $url = $state;
                                        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                                            $url = 'https://'.$url;
                                        }

                                        if (! str_ends_with($url, '/')) {
                                            $url .= '/';
                                        }

                                        $set('canonical_url', $url);
                                    }),
                            ]),

                        Tab::make('Scripts')
                            ->icon('heroicon-o-code-bracket')
                            ->schema([
                                Textarea::make('scripts_header')
                                    ->label('Header Scripts')
                                    ->columnSpanFull(),
                                Textarea::make('scripts_body')
                                    ->label('Body Scripts')
                                    ->columnSpanFull(),
                                Textarea::make('scripts_footer')
                                    ->label('Footer Scripts')
                                    ->columnSpanFull(),
                            ]),

                        Tab::make('Mail Configuration')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                TextInput::make('mail_default_recipient')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('mail_from_address')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('mail_from_name')
                                    ->maxLength(255),
                                TextInput::make('smtp_host')
                                    ->maxLength(255),
                                TextInput::make('smtp_port')
                                    ->numeric(),
                                TextInput::make('smtp_username')
                                    ->maxLength(255),
                                TextInput::make('smtp_password')
                                    ->password()
                                    ->maxLength(255),
                                Select::make('smtp_encryption')
                                    ->options([
                                        'tls' => 'TLS',
                                        'ssl' => 'SSL',
                                    ]),
                            ])->columns(2),

                        Tab::make('Legal')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RichEditor::make('privacy_policy_text')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
