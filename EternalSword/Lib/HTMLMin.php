<?php namespace EternalSword\Lib;

class HTMLMin {
	// works on strings
	public static function html($html) {
		// remove whitespace between tags to avoid issues with inline-block
		$html = preg_replace('/>[\r\n\s]+</', '><', $html);
		$html = preg_replace('/>[\r\n]+/', '>', $html);
		$html = preg_replace('/[\r\n]</', '<', $html);
		return preg_replace('/\t/', '', $html);
	}

	// Works on response object
	public static function live($response) {
		if($response instanceof \Illuminate\Http\Response) {
			$output = $response->getOriginalContent();
			$response->setContent(self::html($output));
		}
	}
}
