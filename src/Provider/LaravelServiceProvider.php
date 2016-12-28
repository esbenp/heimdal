<?php

namespace Optimus\Heimdal\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
        $this->registerAssets();
    }

    private function registerAssets()
    {
        $this->publishes([
            __DIR__.'/../config/optimus.heimdal.php' => config_path('optimus.heimdal.php')
        ]);
    }

    private function loadConfig()
    {
        if ($this->app['config']->get('optimus.heimdal') === null) {
            $this->app['config']->set('optimus.heimdal', require __DIR__.'/../config/optimus.heimdal.php');
        }
    }
}
