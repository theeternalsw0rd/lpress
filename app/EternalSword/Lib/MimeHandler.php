<?php namespace App\EternalSword\Lib;

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

	protected $mime = NULL;
	protected $status_code = 500;

	function __construct($path = NULL, $file_name = NULL, $allowed_mime_parts = NULL) {
		if(is_array($allowed_mime_parts)) {
			$this->$allowed_mime_parts = $allowed_mime_parts;
		}
		if(is_string($path) && is_string($file_name)) {
			$extension = pathinfo($file_name, PATHINFO_EXTENSION);
			$this->setMime($path, $extension);
			$this->updateStatusCode();
		}
	}

	protected function getWoffMime($path) {
		$file = fopen($path, 'rb');
		if($file === FALSE) return '';
		$signature = fread($file, 4);
		fclose($file);
		if(strtolower($signature) == 'woff')
			return 'application/font-woff';
		return NULL;
	}

	public function getMime() {
		return $this->mime;
	}

	public function setMime($path, $extension) {
		$mime = '';
		$mime = $extension == 'woff' ?
			$this->getWoffMime($path) :
			@finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
		// some source files are detected as text/plain
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
		$this->mime = $mime;
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

	public function updateStatusCode() {
		if($this->mime == NULL) {
			$this->status_code = 500;
			return;
		}
		if($this->mime == '') {
			$this->status_code = 404;
			return;
		}
		$mime_parts = explode('/', $this->mime);
		if(!$this->verifyMimeParts($mime_parts)) {
			$this->status_code = 403;
			return;
		}
		$this->status_code = 200;
		return;
	}

	public function getStatusCode() {
		return $this->status_code;
	}
}
