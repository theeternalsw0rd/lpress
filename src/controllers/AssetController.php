<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\Config;
	use Illuminate\Support\Facades\Input;
	
	class AssetController extends BaseController {
		private $allowed_mime_parts = array(
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

		private function verifyPath($segments, $count) {
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

		private function verifyWoff($path) {
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

		private function getMime($path) {
			$segments = explode('.', $path);
			$extension = $segments[count($segments) - 1];
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

		private function sendFile($path, $file_name) {
			$mime = $this->getMime($path);
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			// source files are detected as text/plain
			switch($ext) {
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
			header('Content-Type: ' . $mime);
			if(array_key_exists('download', Input::all())) {
				header('X-Download-Options: noopen'); // disable directly opening download on IE
				header('Content-Disposition: attachment; filename="' . $file_name . '"');
			}
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
			$path = BaseController::getAssetPath($segments[0] == $attachment_config['path']) . $path;
			$file_name = $segments[--$count];
			$this->sendFile($path, $file_name);
		}
	}
?>
