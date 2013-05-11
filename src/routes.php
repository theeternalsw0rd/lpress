<?php namespace EternalSword\LPress;
	
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\App;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Request;
	use Illuminate\Support\Facades\Session;
	
	$route_prefix = Config::get('l-press::route_prefix');
	$route_prefix = $route_prefix == '/' ? '' : $route_prefix;
	Route::filter(
		'theme',
		function() {
			define('DOMAIN', Request::server('HTTP_HOST'));
			$site = NULL;
			try { 
				$site = Site::where('domain', DOMAIN)->first();
			} catch(\Exception $e) {
				$message = $e->getMessage();
				$code = $e->getCode();
				if($code == 2002) {
					echo 'Could not connect to database.';
					die();
				}
				if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
					echo 'Could not find sites table in the database, '
						. 'please ensure all migrations have been run.';
					die();
				}
				echo 'An unexpected error occurred, please try again later.';
				die();
			}
			if(!$site) {
				$site = Site::where('domain', 'wildcard')->first();
			}
			if(!$site) {
				echo 'No valid site found for this domain, ' 
					. 'if this is not on purpose you may need to seed the database, '
					. 'or you have inadvertantly removed the wildcard domain site';
				die();
			}
			try {
				$theme = Theme::find($site->theme_id);
			} catch(\Exception $e) {
				$message = $e->getMessage();
				$code = $e->getCode();
				if(substr_count($message, 'SQLSTATE[42S02]') > 0) {
					echo 'Could not find themes table in the database, '
						. 'please ensure all migrations have been run.';
					die();
				}
				echo 'An unexpected error occurred, please try again later.';
				die();
			}
			define('THEME', $theme ? $theme->slug : 'default');
		}
	);

	Route::filter(
		'login',
		function() {
			$user = Auth::user();
			if(is_null($user)) {
				Session::set('redirect', URL::full());
				return Redirect::to('lpress-login');
			}
		}
	);

	Route::get(
		empty($route_prefix) ? '/' : $route_prefix,
		array(
			'before' => 'theme',
			'as' => 'lpress-index',
			function() {
				$route = Config::get('l-press::route_index');
				return App::make($route['controller'])->{$route['action']}();
			}
		)
	);

	Route::get(
		$route_prefix . 'assets/{path}',
		array(
			'before' => 'theme',
			'uses' => 'EternalSword\LPress\AssetController@getAsset',
			'as' => 'lpress-asset'
		)
	)->where('path', '(.*)');

	Route::get(
		$route_prefix . 'admin',
		array(
			'before' => 'theme|login',
			'as' => 'lpress-admin',
			function() {
				echo "Hello username";

			}
		)
	);

	Route::get(
		$route_prefix . 'login',
		array(
			'before' => 'theme',
			'uses' => 'EternalSword\LPress\AuthenticationController@getLogin',
			'as' => 'lpress-login'
		)
	);

	Route::get(
		$route_prefix . 'logout',
		array(
			'before' => 'theme',
			'uses' => 'EternalSword\LPress\AuthenticationController@getLogout',
			'as' => 'lpress-logout'
		)
	);

	Route::get(
		$route_prefix . 'logout/logged',
		array(
			'before' => 'theme',
			'as' => 'lpress-logout-logged',
			'uses' => 'EternalSword\LPress\AuthenticationController@getLogoutLogged'
		)
	);

	Route::get(
		$route_prefix . 'logout/login',
		array(
			'as' => 'lpress-logout-login',
			function()
			{
				Auth::logout();
				return Redirect::route('lpress-login');
			}
		)
	);

	Route::post(
		$route_prefix . 'login',
		array(
			'before' => 'csrf',
			function() {
				$remember = Input::get('remember');
				if(Auth::attempt(
					array(
						'username' => Input::get('username'),
						'password' => Input::get('password')
					),
					$remember
				)) {
					$route_prefix = Config::get('l-press::route_prefix');
					$redirect = Session::get('redirect', $route_prefix);
					Session::forget('redirect');
					return Redirect::to($redirect);
				}
				Session::put('bad_login', true);
				return Redirect::route('lpress-login');
			}
		)
	);



	/*Route::get('{hierarchy}/{post}', array('as' => 'posts', function($hierarchy, $post) {
		echo $hierarchy;
		echo $post;
	}))->where('hierarchy', '[A-z\d\-\/]+');*/
