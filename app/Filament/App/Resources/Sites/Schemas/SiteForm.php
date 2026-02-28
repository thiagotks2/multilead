<?php

namespace App\Filament\App\Resources\Sites\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Site Settings')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('General')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options(\App\Enums\SiteStatus::class)
                                    ->required()
                                    ->default(\App\Enums\SiteStatus::Development),
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
                                    ->url()
                                    ->maxLength(255),
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
