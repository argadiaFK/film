<?php

namespace App\View\Components;

use App\Models\Ad;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdSlot extends Component
{
    public function __construct(
        public string $slot,
        public string $class = '',
    ) {
    }

    public function render(): View|string
    {
        $ad = Ad::getBySlot($this->slot);

        if (!$ad) {
            return '';
        }

        return view('components.ad-slot', [
            'content' => $ad,
            'class' => $this->class,
        ]);
    }
}
