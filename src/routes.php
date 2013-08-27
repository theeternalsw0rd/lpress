<?php namespace EternalSword\LPress;
    
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Redirect;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Request;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\URL;
    use Illuminate\Support\Facades\View;
    
    $route_prefix = BaseController::getRoutePrefix();
    $admin_route = Config::get('l-press::admin_route');

    function supportsSHA2() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if(preg_match('/(Windows NT 5)|(Windows XP)/i', $user_agent)
            && !preg_match('/firefox/i', $user_agent)
        ) {
            return FALSE;
        }
        return TRUE;
    }

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
            define('PRODUCTION', $site->in_production == 1 ? 'compressed' : 'uncompressed');
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
        'general',
        function() {
            if(Config::get('l-press::require_ssl') && !Request::secure()) {
                if(!Config::get('l-press::ssl_is_sha2') || supportsSHA2())
                    return Redirect::secure(Request::getRequestUri());
                return Redirect::route('lpress-sha2');
            }
        }
    );

    Route::filter(
        'admin',
        function() {
            $user = Auth::user();
            if(is_null($user)) {
                Session::set('redirect', URL::full());
                return Redirect::route('lpress-login');
            }
            if(Config::get('l-press::admin_require_ssl') && !Request::secure()) {
                if(!Config::get('l-press::ssl_is_sha2') || supportsSHA2())
                    return Redirect::secure(Request::getRequestUri());
                return Redirect::route('lpress-sha2');
            }
        }
    );

    Route::filter(
        'login',
        function() {
            if(Config::get('l-press::login_require_ssl') && !Request::secure()) {
                if(!Config::get('l-press::ssl_is_sha2') || supportsSHA2())
                    return Redirect::secure(Request::getRequestUri());
                return Redirect::route('lpress-sha2');
            }
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
        $route_prefix . 'sha2',
        array(
            'before' => 'theme',
            'as' => 'lpress-sha2',
            function() {
                $view_prefix = 'l-press::themes.' . THEME;
                BaseController::setMacros();
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
        $route_prefix . 'assets/{path}',
        array(
            'before' => 'theme',
            'uses' => 'EternalSword\LPress\AssetController@getAsset',
            'as' => 'lpress-asset'
        )
    )->where('path', '(.*)');

    Route::get(
        $route_prefix . 'upload',
        array(
            'before' => 'theme',
            'uses' => 'EternalSword\LPress\UploadController@getURL'
        )
    );
    Route::post(
        $route_prefix . 'upload',
        array(
            'before' => 'theme',
            'uses' => 'EternalSword\LPress\UploadController@postFile'
        )
    );
    Route::delete(
        $route_prefix . 'upload',
        array(
            'before' => 'theme',
            'uses' => 'EternalSword\LPress\UploadController@deleteFile'
        )
    );

    Route::get(
        $route_prefix . $admin_route,
        array(
            'before' => 'theme|admin',
            'as' => 'lpress-admin',
            function() {
                echo "Hello username";

            }
        )
    );

    Route::get(
        $route_prefix . 'login',
        array(
            'before' => 'theme|login',
            'uses' => 'EternalSword\LPress\AuthenticationController@getLogin',
            'as' => 'lpress-login'
        )
    );

    Route::get(
        $route_prefix . 'logout',
        array(
            'before' => 'theme|login',
            'uses' => 'EternalSword\LPress\AuthenticationController@getLogout',
            'as' => 'lpress-logout'
        )
    );

    Route::get(
        $route_prefix . 'logout/logged',
        array(
            'before' => 'theme|login',
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
            'before' => 'csrf|theme',
            'uses' => 'EternalSword\LPress\AuthenticationController@verifyLogin'

        )
    );

    Route::group(array(
        'prefix' => $route_prefix . 'admin',
        'before' => 'theme'
    ), function() {
        Route::get(
            'install',
            array(
                'before' => 'login',
                'as' => 'lpress-installer',
                'uses' => 'EternalSword\LPress\InstallController@getInstaller'
            )
        );
        Route::post(
            'update-user',
            array(
                'as' => 'lpress-user-update',
                'before' => 'csrf',
                'uses' => 'EternalSword\LPress\UserController@updateUser'
            )
        );
    });


    /*Route::get('{hierarchy}/{post}', array('as' => 'posts', function($hierarchy, $post) {
        echo $hierarchy;
        echo $post;
    }))->where('hierarchy', '[A-z\d\-\/]+');*/
