<?php namespace EternalSword\LPress;
	use Illuminate\Routing\Controllers\Controller;
	class ResourceController extends Controller {
		private $allowed_mime_parts = array(
			'image',
			'video',
			'audio',
			'pdf',
			'css',
			'javascript'
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
				if($segment != 'download') {
					$path .= '/' . $segment;
				}
			}
			return $path;
		}

		private function getMime($path) {
			try {
				$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
			} catch(\Exception $e) {
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			return $mime;
		}

		private function sendFile($path, $file_name, $download) {
			$mime = $this->getMime($path);
			$mime_parts = explode('/', $mime);
			$allowed = FALSE;
			foreach($this->allowed_mime_parts as $allowed_mime_part) {
				if(in_array($allowed_mime_part, $mime_parts)) {
					$allowed = TRUE;
				}
			}
			if(!$allowed) {
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			header('Content-type: ' . $mime);
			if($download) {
				header('Content-Disposition: attachment; filename="' . $file_name . '"');
			}
			readfile($path);
		}

		public function getResource($path) {
			if(!defined('THEME')) {
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			$segments = explode('/', $path);
			$count = count($segments);
			$path = $this->verifyPath($segments, $count);
			if($segments[0] == 'uploads') {
				$upload_config = \Config::get('l-press::uploads');
				$upload_path_base = $upload_config['path_base'];
				switch($upload_path_base) {
					case 'package':
						$real_path = PATH . '/' . $upload_config['path'];
						break;
					case 'laravel':
						$real_path = base_path() . '/' . $upload_config['path'];
						break;
					default:
						$real_path = $upload_path_base . '/' . $upload_config['path'];
				}
			}
			else {
				$theme_config = \Config::get('l-press::themes');
				$theme_path_base = $theme_config['path_base'];
				switch($theme_path_base) {
					case 'package':
						$real_path = PATH . '/' . $theme_config['path'];
						break;
					case 'laravel':
						$real_path = base_path() . '/' . $theme_config['path'];
						break;
					default:
						$real_path = $theme_path_base . '/' . $theme_config['path'];
				}
			}
			$download = $segments[--$count] == 'download';
			if($download) {
				$file_name = $segments[--$count];
			}
			else {
				$file_name = $segments[$count];
			}
			$this->sendFile($real_path, $file_name, $download);
		}
	}
?>
