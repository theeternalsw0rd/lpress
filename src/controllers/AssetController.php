<?php namespace EternalSword\LPress;

	use Illuminate\Routing\Controllers\Controller;
	use Illuminate\Support\Facades\Config;
	
	class AssetController extends BaseController {
		private $allowed_mime_parts = array(
			'image',
			'video',
			'audio',
			'pdf',
			'css',
			'javascript',
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
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			// source files are detected as text/plain
			switch($ext) {
				case 'css': {
					$mime = $mime == 'text/plain' ? 'text/css' : $mime;
					break;
				}
				case 'js': {
					$mime = $mime == 'text/plain' ? 'text/javascript' : $mime;
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
				header('HTTP/1.0 404 Not Found');
				echo '<h1>File could not be found</h1>';
				die();
			}
			header('Content-Type: ' . $mime);
			if($download) {
				header('Content-Disposition: attachment; filename="' . $file_name . '"');
			}
			readfile($path);
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
			$path = BaseController::getAssetPath($segments[0] == 'uploads') . $path;
			$download = $segments[--$count] == 'download';
			if($download) {
				$file_name = $segments[--$count];
			}
			else {
				$file_name = $segments[$count];
			}
			$this->sendFile($path, $file_name, $download);
		}
	}
?>