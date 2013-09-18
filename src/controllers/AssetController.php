<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;
	
	class AssetController extends BaseController {
		protected $allowed_mime_parts = array(
			'image',
			'video',
			'audio',
			'pdf',
			'css',
			'javascript',
			'font-woff', // woff webfont
			'vnd.ms-fontobject', // eot webfont
			'x-font-ttf', // ttf webfont
			'plain'
		);

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
					echo '<h1>Access Denied</h1>';
					die();
				}
				$path .= '/' . $segment;
			}
			return $path;
		}

		protected function verifyWoff($path) {
			$file = fopen($path, 'rb');
			if($file === FALSE) return '';
			$signature = fread($file, 4);
			fclose($file);
			if(strtolower($signature) == 'woff')
				return 'application/font-woff';
			header('HTTP/1.0 403 Forbidden');
			echo '<h1>Access Denied</h1>';
			die();
		}

		protected function getMime($path, $extension) {
			$mime = '';
			$mime = $extension == 'woff' ?
				$this->verifyWoff($path) :
				@finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
			if($mime == '') {
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			return $mime;
		}

		protected function sendFile($path, $file_name) {
			$extension = pathinfo($file_name, PATHINFO_EXTENSION);
			$mime = $this->getMime($path, $extension);
			// source files are detected as text/plain
			switch($extension) {
				case 'css': {
					$mime = strpos($mime, 'text') !== FALSE ? 'text/css' : $mime;
					break;
				}
				case 'js': {
					$mime = strpos($mime, 'text') !== FALSE ? 'text/javascript' : $mime;
					break;
				}
			}
			$mime_parts = explode('/', $mime);
			$allowed = FALSE;
			foreach($this->allowed_mime_parts as $allowed_mime_part) {
				if(in_array($allowed_mime_part, $mime_parts)) {
					$allowed = TRUE;
				}
			}
			if(!$allowed) {
				header('HTTP/1.0 403 Forbidden');
				echo '<h1>Mimetype ' . $mime . ' is forbidden.</h1>';
				die();
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
					exit();
				}
			}
			header('Content-Type: ' . $mime);
			header('Content-Length: ' . $this->get_file_size($path));
			header('Last-Modified: ' . $modified);
			header('Expires: Sun, 17-Jan-2038 19:14:07 GMT');
			readfile($path);
			if(extension_loaded('zlib')){ob_end_flush();}
			exit;
		}

		public function getAsset($path) {
			if(!defined('THEME')) {
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			$segments = explode('/', $path);
			$count = count($segments);
			$path = $this->verifyPath($segments, $count);
			$attachment_config = Config::get('l-press::attachments');
			$path = parent::getAssetPath($segments[0] == $attachment_config['path']) . $path;
			$file_name = $segments[--$count];
			$this->sendFile($path, $file_name);
		}
	}
?>
