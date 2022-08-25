<?php

namespace App\View\Components;

use App\Models\Type;
use Illuminate\View\Component;

class TypeDropdown extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.type-dropdown',[
            'types' => Type::all(),
            'currentType' => Type::where('slug', request('type'))->first()

        ]);
    }
}
