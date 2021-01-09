<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Laravel\Jetstream\Http\Livewire\UpdateProfileInformationForm as Component;

class UpdateProfileInformationForm extends Component
{
    public $banner;
    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        dd($updater);
        $this->resetErrorBag();

        if ($this->photo) {
            $this->state = array_merge($this->state, ['photo' => $this->photo]);
        }

        if ($this->banner) {
            $this->state = array_merge($this->state, ['banner' => $this->banner]);
        }

        $updater->update(Auth::user(), $this->state);

        if (isset($this->photo) || isset($this->banner)) {
            return redirect()->route('profile.show');
        }

        $this->emit('saved');

        $this->emit('refresh-navigation-menu');
    }
}
