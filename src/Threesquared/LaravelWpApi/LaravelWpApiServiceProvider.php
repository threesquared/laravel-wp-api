<?php namespace Threesquared\LaravelWpApi;

use Illuminate\Support\ServiceProvider;

class LaravelWpApiServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('wp-api.php'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WpApi::class, function ($app) {
            return new WpApi(
                $this->app['config']->get('wp-api.endpoint'),
                $this->app['config']->get('wp-api.auth')
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['wp-api'];
    }
}
