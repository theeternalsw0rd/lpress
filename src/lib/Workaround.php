<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Request;

class UserAgent {
	public static function getClass() {
		$user_agent = Request::server('HTTP_USER_AGENT');
		if(strpos($user_agent, "Chrome") !== FALSE && strpos($user_agent, "Windows") !== FALSE) {
			$parts = explode(" ", $user_agent);
			foreach($parts as $part) {
				if(strpos($part, "Chrome") !== FALSE) {
					$browser = explode("/", $part);
					$version = explode('.', $browser[1]);
					// will need to do version check when fix is released and does not require flag
					break;
				}
			}
			return " win-chrome-font";
		}
		return "";
	}
}
