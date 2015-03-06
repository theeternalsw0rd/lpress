<?php namespace EternalSword\LPress;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class LPressServiceProvider extends ServiceProvider {

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
		$db_type = Config::get('database.default');
		$db_connections = Config::get('database.connections');
		$db_connections[$db_type]['prefix'] .= Config::get('lpress::settings.db_prefix');
		var_dump($db_connections[$db_type]['prefix']);die;
		Config::set('database.connections', $db_connections);
		Config::set('auth.driver', 'eloquent');
		Config::set('auth.model', 'EternalSword\LPress\User');
		$this->app->validator->resolver(function($translator, $data, $rules, $messages) {
			return new CustomValidator($translator, $data, $rules, $messages);
		});
		define('PATH', dirname(dirname(__DIR__)));
		$this->loadViewsFrom(PATH . '/views', 'l-press');
		$this->loadTranslationsFrom(PATH . '/lang', 'l-press');
		require PATH . '/routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	}

	protected function registerResources()
	{
		$userConfigFile    = app()->configPath().'/lpress/settings.php';
		$packageConfigFile = __DIR__.'/../../config/lpress.php';
		$config            = $this->app['files']->getRequire($packageConfigFile);

		if (file_exists($userConfigFile)) {
			$userConfig = $this->app['files']->getRequire($userConfigFile);
			$config     = array_replace_recursive($config, $userConfig);
		}

		$this->app['config']->set('lpress::settings', $config);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
