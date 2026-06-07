<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Toast extends Component
{
    // view của component toast
    public function render(): View|Closure|string
    {
        return view('components.toast');
    }
}
