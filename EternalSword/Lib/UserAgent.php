<?php namespace EternalSword\Lib;

use Illuminate\Support\Facades\Request;

class UserAgent {
	public static function supportsSHA2() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/(Windows NT 5)|(Windows XP)/i', $user_agent)
			&& !preg_match('/firefox/i', $user_agent)
		) {
			return false;
		}
		return true;
	}

	public static function getClass() {
		$user_agent = Request::server('HTTP_USER_AGENT');
		if(strpos($user_agent, "Chrome") !== false && strpos($user_agent, "Windows") !== false) {
			$parts = explode(" ", $user_agent);
			foreach($parts as $part) {
				if(strpos($part, "Chrome") !== false) {
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
