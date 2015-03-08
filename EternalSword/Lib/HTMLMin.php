<?php namespace EternalSword\Lib;

class HTMLMin {
	// works on strings
	public static function html($html) {
		// remove whitespace between tags to avoid issues with inline-block
		return preg_replace('/>[\r\n\s]*</', '><', $html);
	}

	// Works on response object
	public static function live($response) {
		if($response instanceof \Illuminate\Http\Response) {
			$output = $response->getOriginalContent();
			$response->setContent(self::html($output));
		}
	}
}
