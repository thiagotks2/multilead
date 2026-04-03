<?php

namespace App\Livewire\AppPanel\Clients;

use Livewire\Component;

class ActivityTimeline extends Component
{
    public $activities;

    public function mount($activities)
    {
        $this->activities = $activities;
    }

    public function render()
    {
        return view('livewire.app-panel.clients.activity-timeline');
    }
}
