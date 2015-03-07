<?php namespace EternalSword;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use EternalSword\Lib\UserAgent;

function checkSSL($type = 'all') {
	switch($type) {
		case 'dashboard': {
			$config = 'lpress::settings.dashboard_require_ssl';
			break;
		}
		case 'login': {
			$config = 'lpress::settings.login_require_ssl';
			break;
		}
		default: {
			$config = 'lpress::settings.require_ssl';
		}
	}
	if(Config::get($config) && !Request::secure()) {
		if(!Config::get('lpress::settings.ssl_is_sha2') || UserAgent::supportsSHA2()) {
			return Redirect::secure(Request::getRequestUri());
		}
		return Redirect::route('lpress-sha2');
	}
}
