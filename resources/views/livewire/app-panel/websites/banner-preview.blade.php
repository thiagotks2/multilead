@php
    $record = $record; // Ensuring $record is available
@endphp

<div class="banner-preview-container"
     x-data="{}"
     x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('banner-preview'))]">
    <!-- Background Image with Overlay -->
    <div class="banner-preview-background" 
         style="background-image: url('{{ $record->image_url }}');">
        <div class="banner-preview-overlay"></div>
    </div>

    <!-- Content Overlay -->
    <div class="banner-preview-content">
        @if($record->title)
            <h2 class="banner-preview-title">
                {{ $record->title }}
            </h2>
        @endif

        @if($record->description)
            <p class="banner-preview-description">
                {{ $record->description }}
            </p>
        @endif

        @if($record->link_url)
            <div class="banner-preview-button-container">
                <a href="{{ $record->link_url }}" target="_blank" class="banner-preview-button">
                    {{ $record->action_label ?: 'Saiba Mais' }}
                </a>
            </div>
        @endif

        @if(!$record->title && !$record->description && !$record->link_url)
            <span class="banner-preview-empty">
                Conteúdo opcional não preenchido
            </span>
        @endif
    </div>

    <!-- Badge Local/Tipo -->
    <div class="banner-preview-badge-container">
        <span class="banner-preview-badge">
            {{ $record->type->getLabel() }}
        </span>
    </div>
</div>
