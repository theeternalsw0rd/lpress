<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\HTML;
	use Illuminate\Support\Facades\Form;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\URL;

	class BaseController extends Controller {
		public static function getRoutePrefix() {
			$route_prefix = Config::get('l-press::route_prefix');
			$route_prefix = $route_prefix == '/' ? '' : $route_prefix;
			return $route_prefix;
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
			Form::macro('faux_file', function($name, $label, $attributes = array()) {
				return "
					<div class='upload'>
						<input id='${name}' name='${name}' data-label='${label}' class='file' type='file'" . self::getAttributeString($attributes) . " />
					</div>
				";
			});
			HTML::macro('asset', function($type, $path, $attributes = array()) {
				$asset_domain = Config::get('l-press::asset_domain');
				$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
				$open = '';
				$close = '';
				switch($type) {
					case 'css': {
						$path = "css/" . PRODUCTION . '/' . $path;
						$open .= "<link rel='stylesheet' type='text/css' href='//" . $asset_domain ."/assets/" . $path . "?v=";
						$close .= "'>";
						break;
					}
					case 'js': {
						$path = "js/" . PRODUCTION . '/' . $path;
						$open .= "<script type='text/javascript' src='//" . $asset_domain . "/assets/" . $path . "?v=";
						$close .= "'></script>";
						break;
					}
					case 'img': {
						$open .= "<img src='//" . $asset_domain . "/assets" . $path . "?v=";
						$close .= "'" . self::getAttributeString($attributes) . "/>";
						break;
					}
					default: {
						break;
					}
				}
				$version = '';
				$version = @filemtime(BaseController::getAssetPath() . '/' . $path);
				if($version == '') {
					$close = "' data-err='$path could not be found" . $close;
				}
				return $open . $version . $close;
			});
		}

		public static function getAssetPath($upload = FALSE) {
			$path = '';
			if($upload) {
				$upload_config = Config::get('l-press::uploads');
				$upload_path_base = $upload_config['path_base'];
				switch($upload_path_base) {
					case 'package': {
						$path = PATH . '/' . $upload_config['path'] . '/';
						break;
					}
					case 'laravel': {
						$path = base_path() . '/' . $upload_config['path'] . '/';
						break;
					}
					default: {
						$path = $upload_path_base . '/' . $upload_config['path'] . '/';
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
	}
