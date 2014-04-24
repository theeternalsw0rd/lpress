<?php namespace EternalSword\LPress;

use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

class AssetController extends BaseController {
	// Start from Blueimp UploadHandler
	// Fix for overflowing signed 32 bit integers,
	// works for sizes up to 2^32-1 bytes (4 GiB - 1):
	protected function fix_integer_overflow($size) {
		if ($size < 0) {
			$size += 2.0 * (PHP_INT_MAX + 1);
		}
		return $size;
	}

	protected function get_file_size($file_path, $clear_stat_cache = false) {
		if ($clear_stat_cache) {
			clearstatcache(true, $file_path);
		}
		return $this->fix_integer_overflow(filesize($file_path));
	}
	// End from Blueimp UploadHandler

	protected function verifyPath($segments, $count) {
		$path = '';
		$i=0;
		while($i < $count) {
			$segment = $segments[$i++];
			if(substr($segment, 0) == '.') {
				header('HTTP/1.0 403 Forbidden');
				echo '<h1>'.Lang::get('l-press::errors.pathPermissionError').'</h1>';
				die;
			}
			$path .= '/' . $segment;
		}
		return $path;
	}

	protected function sendFile($path, $file_name) {
		$mime_handler = new MimeHandler($path, $file_name);
		$status_code = $mime_handler->getStatusCode();
		$mime = $mime_handler->getMime();
		switch($status_code) {
			case 200: {
				break;
			}
			case 403: {
				App::abort($status_code, Lang::get('l-press::errors.mimePermissionError', array('mime' => $mime)));
				die;
			}
			default: {
				App::abort($status_code);
				die;
			}
		}
		if(extension_loaded('zlib')){ob_start('ob_gzhandler');}
		$modified = gmdate('D, d M Y H:i:s T', filemtime($path));
		if(array_key_exists('download', Input::all())) {
			header('X-Download-Options: noopen'); // disable directly opening download on IE
			header('Content-Disposition: attachment; filename="' . $file_name . '"');
		}
		else {
			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $modified) {
				header('HTTP/1.1 304 Not Modified');
				die;
			}
		}
		header('Content-Type: ' . $mime);
		header('Content-Length: ' . $this->get_file_size($path));
		header('Last-Modified: ' . $modified);
		header('Expires: Sun, 17-Jan-2038 19:14:07 GMT');
		readfile($path);
		if(extension_loaded('zlib')){ob_end_flush();}
		die;
	}

	public function getAsset($path) {
		if(!defined('THEME')) {
			header('HTTP/1.0 404 Not Found');
			echo '<h1>'.Lang::get('l-press::errors.assetNotFound').'</h1>';
			die;
		}
		$segments = explode('/', $path);
		$count = count($segments);
		$path = $this->verifyPath($segments, $count);
		$attachment_config = Config::get('l-press::attachments');
		$path = self::getAssetPath($segments[0] == $attachment_config['path']) . $path;
		$file_name = $segments[--$count];
		$this->sendFile($path, $file_name);
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

}
