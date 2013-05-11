<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\View;
	use Illuminate\Support\Facades\Html;
	use Illuminate\Support\Facades\Config;

	class BaseController extends Controller {
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
