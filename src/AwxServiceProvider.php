<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Awx.
 *
 * (c) Graham Campbell <graham@alt-three.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sdwru\Awx;

use AwxV2\AwxV2;
use AwxV2\Oauth\Oauth2;
use Sdwru\Awx\Adapter\ConnectionFactory as AdapterFactory;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the awx service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class AwxServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath($raw = __DIR__.'/../config/awx.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('awx.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('awx');
        }

        $this->mergeConfigFrom($source, 'awx');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAdapterFactory();
        $this->registerAwxFactory();
        $this->registerAwxOauthWrapper();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the adapter factory class.
     *
     * @return void
     */
    protected function registerAdapterFactory()
    {
        $this->app->singleton('awx.adapterfactory', function () {
            return new AdapterFactory();
        });

        $this->app->alias('awx.adapterfactory', AdapterFactory::class);
    }

    /**
     * Register the awx factory class.
     *
     * @return void
     */
    protected function registerAwxFactory()
    {
        $this->app->singleton('awx.factory', function (Container $app) {
            $adapter = $app['awx.adapterfactory'];

            return new AwxFactory($adapter);
        });

        $this->app->alias('awx.factory', AwxFactory::class);
    }
    
    /**
     * Register the awx oauth token class.
     *
     * @return void
     */
    protected function registerAwxOauthWrapper()
    {
        $this->app->singleton('awx.oauthWrapper', function (Container $app) {
            return new AwxOauthWrapper();
        });

        $this->app->alias('awx.oauthWrapper', AwxOauthWrapper::class);
    }


    /**
     * Register the manager class.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('awx', function (Container $app) {
            $config = $app['config'];
            $factory = $app['awx.factory'];
            $oauthWrapper = $app['awx.oauthWrapper'];

            return new AwxManager($config, $factory, $oauthWrapper);
        });

        $this->app->alias('awx', AwxManager::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind('awx.connection', function (Container $app) {
            $manager = $app['awx'];

            return $manager->connection();
        });

        $this->app->alias('awx.connection', AwxV2::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'awx.adapterfactory',
            'awx.factory',
            'awx.oauthWrapper',
            'awx',
            'awx.connection',
        ];
    }
}
