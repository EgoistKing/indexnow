<?php

namespace Egoist\IndexNow\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use Egoist\IndexNow\IndexNow;
use Egoist\IndexNow\Interfaces\IndexNowInterface;

class IndexNowServiceProvider extends ServiceProvider
{

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../config/index-now.php' => config_path('index-now.php'),
		], 'index-now');

		if (!$this->app->configurationIsCached()) {
			$this->mergeConfigFrom(__DIR__ . '/../../config/index-now.php', 'index-now');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(IndexNowInterface::class, function ($app) {
			return new IndexNow();
		});
	}
}
