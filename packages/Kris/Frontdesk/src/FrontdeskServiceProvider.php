<?php

/**
 * FrontdeskServiceProvider short summary.
 *
 * FrontdeskServiceProvider description.
 *
 * @version 1.0
 * @author kris
 */

namespace Kris\Frontdesk;
use \Illuminate\Support\ServiceProvider;

class FrontdeskServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('Frontdesk',function($app){
            return new Frontdesk;
        });

        $this->app->alias('FO', 'Kris\Frontdesk\Helpers');
        $this->registerHelper();
    }

    public function registerHelper()
    {
        $this->app->bindShared('FO', function($app)
		{
			return new Helpers();
		});
    }
    public function boot()
    {
        require __DIR__."/Http/routes.php";
        $this->loadViewsFrom(__DIR__."../../views","Frontdesk");
    }

    public function provides()
	{
		return array('FOUser');
	}
}