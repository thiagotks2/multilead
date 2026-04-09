<?php

namespace App\Filament\App\Resources\Websites\BannerResource\Schemas;

use App\Modules\Websites\Enums\BannerType;
use App\Modules\Websites\Rules\BannerMediaRule;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BannerSchema
{
    public static function make(Schema $schema): Schema
    {
        return $schema
            ->components([

                FileUpload::make('image_path')
                    ->label('Banner Image')
                    ->image()
                    ->disk('public')
                    ->maxSize(10240)
                    ->rule(new BannerMediaRule)
                    ->directory(fn ($livewire) => filament()->getTenant()->id.'/'.$livewire->site.'/banners')
                    ->required()
                    ->visibility('public')
                    ->columnSpanFull(),

                TextInput::make('title')
                    ->maxLength(255)
                    ->columnSpanFull(), // Optional based on BR05

                Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                TextInput::make('link_url')
                    ->url()
                    ->maxLength(255),

                TextInput::make('action_label')
                    ->maxLength(255),

                Select::make('type')
                    ->options(BannerType::class)
                    ->required()
                    ->native(false)
                    ->default(BannerType::General->value),

                DateTimePicker::make('display_until'),
            ]);
    }
}
