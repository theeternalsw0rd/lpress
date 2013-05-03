<?php
Route::filter(
	'theme',
	function() {
		define('DOMAIN', Request::server('HTTP_HOST'));
		$site = NULL;
		try { 
			$site = DB::table('sites')->where('domain', DOMAIN)->first();
		} catch(Exception $e) {
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
			$site = DB::table('sites')->where('domain', '*')->first();
		}
		if(!$site) {
			echo 'No valid site found for this domain, ' 
				. 'if this is not on purpose you may need to seed the database, '
				. 'or you have inadvertantly removed the wildcard (*) domain site';
			die();
		}
		try {
			$theme = DB::table('themes')->where('id', $site->theme_id)->first();
		} catch(Exception $e) {
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

Route::get(
	'/',
	array(
		'before' => 'theme',
		'uses' => 'EternalSword\LPress\IndexController@getIndex',
		'as' => 'index'
	)
);
Route::get(
	'resources/{path}',
	array(
		'before' => 'theme',
		'uses' => 'EternalSword\LPress\ResourceController@getResource',
		'as' => 'resource'
	)
)->where('path', '(.*)');
/*Route::get('{hierarchy}/{post}', array('as' => 'posts', function($hierarchy, $post) {
	echo $hierarchy;
	echo $post;
}))->where('hierarchy', '[A-z\d\-\/]+');*/
