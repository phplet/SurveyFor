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
        $title = !empty($view->getData()["title"]) ? $view->getData()["title"] : "";
        $view->with('title', $title);
    }
}