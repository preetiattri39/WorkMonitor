<?php

namespace App\Livewire\Dashboard;

use App\Models\Attendance;
use Livewire\Component;

class PresenceWidget extends Component
{
    public function render()
    {
        return view('livewire.dashboard.presence-widget', [
            'presentCount' => Attendance::whereDate('date', today())->where('status', '!=', 'absent')->count(),
            'absentCount' => Attendance::whereDate('date', today())->where('status', 'absent')->count(),
        ]);
    }
}
