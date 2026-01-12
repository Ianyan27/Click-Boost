<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ViewModal extends Component
{
    /**
     * Create a new component instance.
     */

    public string $entity;
    public string $modalId;

    public function __construct(string $entity, string $modalId)
    {
        $this->entity = $entity;
        $this->modalId = $modalId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.view-modal');
    }
}
