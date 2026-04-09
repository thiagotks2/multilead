<?php

namespace App\Livewire\AppPanel\Websites;

use App\Modules\Websites\Models\SiteBanner;
use Livewire\Component;

class BannerPreview extends Component
{
    public SiteBanner $record;

    public function mount(SiteBanner $record): void
    {
        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.app-panel.websites.banner-preview');
    }
}
