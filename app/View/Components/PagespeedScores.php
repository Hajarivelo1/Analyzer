<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PagespeedScores extends Component
{
    public array $scores;

    public function __construct(array $scores)
    {
        $this->scores = $scores;
    }

    public function render()
    {
        return view('components.pagespeed-scores');
    }
}
