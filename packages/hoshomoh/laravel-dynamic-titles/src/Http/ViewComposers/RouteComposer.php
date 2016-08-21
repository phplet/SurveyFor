<?php

namespace hoshomoh\LaravelDynamicTitles\Http\ViewComposers;

use Illuminate\Routing\Route;
use Illuminate\View\View;

class RouteComposer
{
    protected $route;

    /**
     * Create a new route composer.
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        // Dependencies automatically resolved by service container...
        $this->route = $route;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        var_dump($this->route->getName());
        $view->with('title', "Awesome");
    }
}