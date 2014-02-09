<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

$route_prefix = BaseController::getRoutePrefix();
$dashboard_route = '+' . Config::get('l-press::dashboard_route');

// filtering placement thanks to http://markvaneijk.com/minify-the-html-output-in-laravel-4
App::after(function($request, $response) {
	if($response instanceof \Illuminate\Http\Response) {
		$output = $response->getOriginalContent();
		// remove whitespace between tags to avoid issues with inline-block
		$output = preg_replace('/>[\r\n\s]*</', '><', $output);
		$response->setContent($output);
	}
});

App::error(function(HttpException $exception) {
	// missing can't take filters like routes, so call needed stuff directly.
	extract(BaseController::prepareMake());
	$status_code = $exception->getStatusCode();
	$message = $exception->getMessage() ?: 'An error occurred and your request could not be processed.';
	return Response::view($view_prefix . '.errors', array(
		'view_prefix' => $view_prefix,
		'title' => 'HttpError: ' + $status_code,
		'status_code' => $status_code,
		'message' => $message
	), $status_code);
});

App::error(function(\Illuminate\Session\TokenMismatchException $exception) {
	$status_code = 403;
	$message = 'Permission denied. Tokens do not match.';
	if(Request::ajax()) {
		$json = new \stdClass;
		$json->error = $message;
		return Response::json($json, $status_code);
	}
	return Response::view($view_prefix . '.errors', array(
		'view_prefix' => $view_prefix,
		'title' => 'HttpError: ' + $status_code,
		'status_code' => $status_code,
		'message' => $message
	), $status_code);
});

Route::filter(
	'theme',
	function() {
		BaseController::verifyTheme();
	}
);

Route::filter(
	'general',
	function() {
		return BaseController::checkSSL();
	}
);

Route::filter(
	'dashboard',
	function() {
		if(!Auth::check()) {
			if(Request::ajax()) {
				$json = new \stdClass;
				$json->error = "Permission denied. Not logged in.";
				$status_code = 403;
				return Response::json($json, $status_code);
			}
			Session::set('redirect', URL::full());
			return Redirect::route('lpress-login');
		}
		return BaseController::checkSSL('dashboard');
	}
);

Route::filter(
	'login-ssl',
	function() {
		return BaseController::checkSSL('login');
	}
);

Route::get(
	empty($route_prefix) ? '/' : $route_prefix,
	array(
		'before' => 'theme|general',
		'as' => 'lpress-index',
		function() {
			$route = Config::get('l-press::route_index');
			return App::make($route['controller'])->{$route['action']}();
		}
	)
);

Route::get(
	$route_prefix . '+sha2',
	array(
		'before' => 'theme',
		'as' => 'lpress-sha2',
		function() {
			extract(BaseController::prepareMake());
			return View::make($view_prefix . '.sha2', 
				array(
					'view_prefix' => $view_prefix,
					'title' => 'SSL Requires SHA2',
					'route_prefix' => Config::get('l-press::route_prefix')
				)
			);
		}
	)
);

Route::get(
	$route_prefix . '+assets/{path}',
	array(
		'before' => 'theme',
		'uses' => 'EternalSword\LPress\AssetController@getAsset',
		'as' => 'lpress-asset'
	)
)->where('path', '(.*)');

Route::get(
	$route_prefix . '+upload',
	array(
		'before' => 'csrf|theme|dashboard',
		'uses' => 'EternalSword\LPress\UploadController@getURL'
	)
);
Route::post(
	$route_prefix . '+upload',
	array(
		'before' => 'csrf|theme|dashboard',
		'uses' => 'EternalSword\LPress\UploadController@postFile'
	)
);
Route::delete(
	$route_prefix . '+upload',
	array(
		'before' => 'csrf|theme|dashboard',
		'uses' => 'EternalSword\LPress\UploadController@deleteFile'
	)
);

Route::get(
	$route_prefix . '+login',
	array(
		'before' => 'theme|login-ssl',
		'uses' => 'EternalSword\LPress\AuthenticationController@getLogin',
		'as' => 'lpress-login'
	)
);

Route::get(
	$route_prefix . '+logout',
	array(
		'before' => 'theme|login-ssl',
		'uses' => 'EternalSword\LPress\AuthenticationController@getLogout',
		'as' => 'lpress-logout'
	)
);

Route::get(
	$route_prefix . '+logout/logged',
	array(
		'before' => 'theme|login-ssl',
		'as' => 'lpress-logout-logged',
		'uses' => 'EternalSword\LPress\AuthenticationController@getLogoutLogged'
	)
);

Route::get(
	$route_prefix . '+logout/login',
	array(
		'as' => 'lpress-logout-login',
		function() {
			Auth::logout();
			return Redirect::route('lpress-login');
		}
	)
);

Route::post(
	$route_prefix . '+login',
	array(
		'before' => 'csrf|theme',
		'uses' => 'EternalSword\LPress\AuthenticationController@verifyLogin'
	)
);

Route::group(
	array(
		'prefix' => $route_prefix . $dashboard_route,
		'before' => 'theme|dashboard'
	), 
	function() {
		Route::get(
			'/',
			array(
				'before' => 'theme|dashboard',
				'as' => 'lpress-dashboard',
				'uses' => 'EternalSword\LPress\DashboardController@getDashboard'
			)
		);
		Route::get(
			'install',
			array(
				'as' => 'lpress-installer',
				'uses' => 'EternalSword\LPress\InstallController@getInstaller'
			)
		);
		Route::post(
			'update-user',
			array(
				'as' => 'lpress-update-user',
				'before' => 'csrf',
				'uses' => 'EternalSword\LPress\UserController@updateUser'
			)
		);
	}
);

Route::group(
	array(
		'prefix' => $route_prefix . '/+record',
		'before' => 'theme|dashboard'
	),
	function() {
		Route::get(
			'create',
			array(
				'uses' => 'EternalSword\LPress\RecordController@getRecordForm'
			)
		);
		Route::post(
			'create',
			array(
				'uses' => 'EternalSword\LPress\RecordController@createRecord'
			)
		);
	}
);

Route::get(
	'{path}',
	array(
		'before' => 'theme|general',
		'as' => 'records',
		'uses' => 'EternalSword\LPress\RecordController@parseRoute'
	)
)->where('path', '[A-z\d\-\/\.]+');
