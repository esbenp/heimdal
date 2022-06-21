<?php

namespace Digitalroll\Heimdal\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Digitalroll\Heimdal\Reporters\BugsnagReporter;
use Digitalroll\Heimdal\Reporters\RollbarReporter;
use Digitalroll\Heimdal\Reporters\SentryReporter;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {
        $this->loadConfig();
        $this->registerAssets();
        $this->bindReporters();
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

    private function bindReporters()
    {
        $this->app->bind(BugsnagReporter::class, function ($app) {
            return function (array $config) {
                return new BugsnagReporter($config);
            };
        });

        $this->app->bind(SentryReporter::class, function ($app) {
            return function (array $config) {
                return new SentryReporter($config);
            };
        });

        $this->app->bind(RollbarReporter::class, function ($app) {
            return function (array $config) {
                return new RollbarReporter($config);
            };
        });
    }
}
