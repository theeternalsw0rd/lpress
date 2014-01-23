<?php namespace EternalSword\LPress;

class MimeHandler {
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
		return $mime;
	}

	protected function verifyMimeParts($mime_parts) {
		$allowed = FALSE;
		foreach($this->allowed_mime_parts as $allowed_mime_part) {
			if(in_array($allowed_mime_part, $mime_parts)) {
				$allowed = TRUE;
			}
		}
		return $allowed;
	}

	public function verifyMime($path, $file_name) {
		$extension = pathinfo($file_name, PATHINFO_EXTENSION);
		$mime = $this->getMime($path, $extension);
		$mime_parts = explode('/', $mime);
		if(!$this->verifyMimeParts($mime_parts)) {
			header('HTTP/1.0 403 Forbidden');
			echo '<h1>Mimetype ' . $mime . ' is forbidden.</h1>';
			die();
		}
		return $mime;
	}
}
