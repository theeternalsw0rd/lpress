<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\HTML;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class BaseController extends Controller {
	private function supportsSHA2() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/(Windows NT 5)|(Windows XP)/i', $user_agent)
			&& !preg_match('/firefox/i', $user_agent)
		) {
			return FALSE;
		}
		return TRUE;
	}

	public static function getRoutePrefix() {
		$route_prefix = Config::get('l-press::route_prefix');
		$route_prefix = $route_prefix == '/' ? '' : $route_prefix;
		return $route_prefix;
	}

	public static function verifyTheme() {
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
		define('SITE', $site->id);
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

	public static function checkSSL($type = 'all') {
		switch($type) {
			case 'admin': {
				$config = 'l-press::admin_require_ssl';
				break;
			}
			case 'login': {
				$config = 'l-press::login_require_ssl';
				break;
			}
			default: {
				$config = 'l-press::require_ssl';
			}
		}
		if(Config::get($config) && !Request::secure()) {
			if(!Config::get('l-press::ssl_is_sha2') || $this->supportsSHA2()) {
				return Redirect::secure(Request::getRequestUri());
			}
			return Redirect::route('lpress-sha2');
		}
	}

	public static function getAttributeString($attributes) {
		$attribute_string = '';
		if(is_array($attributes) && count($attributes) > 0) {
			foreach($attributes as $attribute => $value) {
				$attribute_string .= " ${attribute}='${value}'";
			}
		}
		return $attribute_string;
	}

	public static function setMacros() {
		HTML::macro('url', function($url, $text = null, $attributes = array()) {
			$attribute_string = '';
			$has_title = FALSE;
			if(is_array($attributes) && count($attributes) > 0) {
				foreach($attributes as $attribute => $value) {
					if($attribute == 'title') {
						$title = $value;
						$has_title = TRUE;
					}
					else {
						$attribute_string .= " ${attribute}='${value}'";
					}
				}
			}
			$text = is_null($text) ? $url : $text;
			$title = $has_title ? $title : $text;
			return "<a href='${url}' title='${title}'${attribute_string}>${title}</a>";
		});
		Form::macro('faux_checkbox', function($name, $label, $attributes = array()) {
			return "
				<label for='${name}' class='checkbox'>
					<input id='${name}' name='${name}' class='checkbox' type='checkbox'" . self::getAttributeString($attributes) . " />
					<span unselectable='on' class='checkbox-label' data-for='${name}'>${label}</span>
				</label>
			";
		});
		Form::macro('faux_file', function($slug, $attributes = array()) {
			$type = RecordType::where('slug', '=', $slug)->first();
			if(count($type) === 0) {
				return "<div class='error'>Could not find RecordType ${slug} for file input.</div>";
			}
			$label = "Select {$type->label}";
			$file_path = $slug;
			$url_path = $slug;
			while($type->depth > 1) {
				$type = $type->parent_type()->first();
				$file_path = $type->slug . '/' . $file_path;
				$url_path = $type->slug . '/' . $url_path;
			}
			$site = Site::find(SITE);
			$root_type = $type->parent_type()->first();
			$file_path = $root_type->slug . '/' . $site->domain . '/' . $file_path;
			if ($root_type->slug != 'attachments') {
				return "<div class='error'>RecordType ${slug} is not valid for file input.</div>";
			}
			$prefix = self::getRoutePrefix();
			$url = $prefix . "/upload?path=${file_path}/&uri=${prefix}/${url_path}/";
			$url_path = $prefix . '/' . $url_path;
			$attributes = self::getAttributeString($attributes);
			return "<a href='#${slug}' title='${label}' data-prefix='${prefix}' data-path='${url_path}' data-url='${url}' class='single file' ${attributes}>${label}</a>";
		});
		HTML::macro('asset', function($type, $path, $attributes = array()) {
			$asset_domain = Config::get('l-press::asset_domain');
			$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
			$route_prefix = self::getRoutePrefix();
			$open = '';
			$close = '';
			switch($type) {
				case 'css': {
					$path = "css/" . PRODUCTION . '/' . $path;
					$open .= "<link rel='stylesheet' type='text/css' href='//" . $asset_domain . $route_prefix ."/assets/" . $path . "?v=";
					$close .= "'>";
					break;
				}
				case 'js': {
					$path = "js/" . PRODUCTION . '/' . $path;
					$open .= "<script type='text/javascript' src='//" . $asset_domain . $route_prefix . "/assets/" . $path . "?v=";
					$close .= "'></script>";
					break;
				}
				case 'img': {
					$open .= "<img src='//" . $asset_domain . $route_prefix . "/assets" . $path . "?v=";
					$close .= "'" . self::getAttributeString($attributes) . "/>";
					break;
				}
				default: {
					break;
				}
			}
			$version = '';
			$version = @filemtime(self::getAssetPath() . '/' . $path);
			if($version == '') {
				$close = "' data-err='$path could not be found" . $close;
			}
			return $open . $version . $close;
		});
	}

	public static function getAssetPath($attachment = FALSE) {
		$path = '';
		if($attachment) {
			$attachment_config = Config::get('l-press::attachments');
			$attachment_path_base = $attachment_config['path_base'];
			switch($attachment_path_base) {
				case 'package': {
					$path = PATH . '/';
					break;
				}
				case 'laravel': {
					$path = base_path() . '/';
					break;
				}
				default: {
					$path = $attachment_path_base . '/';
				}
			}
		}
		else {
			$theme_config = Config::get('l-press::themes');
			$theme_path_base = $theme_config['path_base'];
			switch($theme_path_base) {
				case 'package':
					$path = PATH . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				case 'laravel':
					$path = base_path() . '/' . $theme_config['path'] . '/' . THEME . '/assets';
					break;
				default:
					$path = $theme_path_base . '/' . $theme_config['path'] . '/' . THEME . '/assets';
			}
		}
		return $path;
	}

	public static function prepareMake() {
		if(!defined('THEME')) {
			echo 'An unknown error occured, please try again later.';
			die();
		}
		$view_prefix = 'l-press::themes.' . THEME . '.templates';
		self::setMacros();
		return array("view_prefix" => $view_prefix, "site" => Site::find(SITE));
	}

	public static function slugsToRoute($path) {
		$route = new \stdClass;
		$route->throw404 = FALSE;
		$route->json = FALSE;
		$slugIsValidRecordType = function($i, $last_index, $segments, $slug) use(&$route) {
			$record_type = RecordType::where('slug', '=', $slug)->first();
			if(count($record_type) === 0) {
				return FALSE;
			}
			$parent = RecordType::find($record_type->parent_id);
			if($i > 0) {
				if($parent->depth > 0 && $parent->slug != $segments[$i-1]) {
					return FALSE;
				}
			}
			else {
				$route->root_record_type = $parent;
			}
			if($i >= $last_index) {
				$route->record_type = $record_type;
			}
			return TRUE;
		};
		$segments = preg_split('@/@', $path, NULL, PREG_SPLIT_NO_EMPTY);
		$slugs = array();
		$slug_types = array();
		$last_index = count($segments) - 1;
		$last_slug = $segments[$last_index];
		$last_slug = explode('.', $last_slug);
		if(count($last_slug) > 1 && $last_slug[1] == 'json') {
			$segments[$last_index] = $last_slug[0];
			$route->json = TRUE;
		}
		for($i=$last_index;$i>=0;$i--) {
			$slug = $segments[$i];
			if($i == $last_index) {
				$records = Record::where('slug', '=', $slug)->get();
				if(count($records) === 0) {
					$route->throw404 = !$slugIsValidRecordType($i, $last_index, $segments, $slug);
					if($route->throw404) {
						break;
					}
					array_push($slug_types, 'record_type');
				}
				else {
					$route->records = $records;
					if($i > 0) {
						$found = FALSE;
						foreach($records as $record) {
							$record_type_slug = RecordType::find($record->record_type_id)->slug;
							if($segments[$i-1] == $record_type_slug) {
								$found = TRUE;
								$route->record = $record;
								break;
							}
						}
						$route->throw404 = !$found;
						if(!$found) {
							break;
						}
					}
					array_push($slug_types, 'record');
				}
			}
			else {
				$route->throw404 = !$slugIsValidRecordType($i, $last_index, $segments, $slug);
				if($route->throw404) {
					break;
				}
				array_push($slug_types, 'record_type');
			}
			array_push($slugs, $slug);
		}
		$route->slug_types = $slug_types;
		$route->slugs = $slugs;
		return $route;
	}
}
