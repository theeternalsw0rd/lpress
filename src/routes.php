<?php namespace EternalSword\LPress;
	
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Support\Facades\Route;
	use Illuminate\Support\Facades\App;
	use Illuminate\Support\Facades\Redirect;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Request;
	use Illuminate\Support\Facades\Session;
	
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
			if($theme) {
				define('THEME', $theme->slug);
				return;
			}
			define('THEME', 'default');
		}
	);

	Route::filter(
		'login',
		function($route, $request, $route_name) {
			$user = Auth::user();
			if(is_null($user)) {
				return Redirect::to('login/' . $route_name);
			}
		}
	);

	Route::get(
		'/',
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
		'resources/{path}',
		array(
			'before' => 'theme',
			'uses' => 'EternalSword\LPress\ResourceController@getResource',
			'as' => 'lpress-resource'
		)
	)->where('path', '(.*)');

	Route::get(
		'admin',
		array(
			'before' => 'theme|login:lpress-admin',
			'as' => 'lpress-admin',
			function() {
				echo "Hello username";
			}
		)
	);

	Route::get(
		'login',
		function() {
			return Redirect::to('login/lpress-index');
		}
	);

	Route::get(
		'login/{source}',
		array(
			'before' => 'theme',
			'uses' => 'EternalSword\LPress\LoginController@getLogin',
			'as' => 'lpress-login'
		)
	);

	Route::post(
		'login/{source}',
		array(
			'before' => 'csrf',
			function($return_route) {
				$remember = Input::get('remember');
				if(Auth::attempt(
					array(
						'username' => Input::get('username'),
						'password' => Input::get('password')
					),
					$remember
				)) {
					return Redirect::route($return_route);
				}
				Session::put('bad_login', true);
				return Redirect::to('login/' . $return_route);
			}
		)
	);

	/*Route::get('{hierarchy}/{post}', array('as' => 'posts', function($hierarchy, $post) {
		echo $hierarchy;
		echo $post;
	}))->where('hierarchy', '[A-z\d\-\/]+');*/
