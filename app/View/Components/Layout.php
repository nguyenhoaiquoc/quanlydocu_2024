<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Category;

class Layout extends Component
{
    public $isMainLayout; // Thêm dòng này

    /**
     * Create a new component instance.
     */
    public function __construct(
       
        $isMainLayout = false) // Nhận biến
    {
        
        $this->isMainLayout = $isMainLayout;
    }

    /**
     * Get the view / contents that represent the component.
     */
     public function render(): View|Closure|string
    {
        $categories = Category::all();
        return view('components.layout', ['categories' => $categories]);
    }
}
