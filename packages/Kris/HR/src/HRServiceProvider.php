<?php

/**
 * FrontdeskServiceProvider short summary.
 *
 * FrontdeskServiceProvider description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\HR;
use \Illuminate\Support\ServiceProvider;

class HRServiceProvider extends ServiceProvider
{
    public function register()
    {
       // $this->app->alias('FO', 'Kris\Frontdesk\Helpers');
        //$this->registerHelper();
    }

    public function boot()
    {
        require __DIR__."/Http/routes.php";
        $this->loadViewsFrom(__DIR__."/../views/","HR");
    }
}
