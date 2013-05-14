<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Html;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\URL;

	class BaseController extends Controller {
		protected function setMacros() {
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
							$attribute_string .= " $attribute='$value'";
						}
					}
				}
				$text = is_null($text) ? $url : $text;
				$title = $has_title ? $title : $text;
				return "<a href='$url' title='$title'$attribute_string>$title</a>";
			});
			HTML::macro('asset', function($type, $path, $attributes = array()) {
				$open = '';
				$close = '';
				switch($type) {
					case 'css': {
						$path = "css/" . PRODUCTION . '/' . $path;
						$open .= "<link rel='stylesheet' type='text/css' href='/assets/" . $path . "?v=";
						$close .= "'>";
						break;
					}
					case 'js': {
						$path = "js/" . PRODUCTION . '/' . $path;
						$open .= "<script type='text/javascript' src='/assets/" . $path . "?v=";
						$close .= "'></script>";
						break;
					}
					case 'img': {
						$attribute_string = '';
						if(is_array($attributes) && count($attributes) > 0) {
							foreach($attributes as $attribute => $value) {
								$attribute_string .= " $attribute='$value'";
							}
						}
						$open .= "<img src='/assets" . $path . "?v=";
						$close .= "'" . $attribute_string . "/>";
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
