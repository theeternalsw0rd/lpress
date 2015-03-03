<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

function checkSSL($type = 'all') {
	switch($type) {
		case 'dashboard': {
			$config = 'l-press::dashboard_require_ssl';
			break;
		}
		case 'login': {
			$config = 'l-press::login_require_ssl';
			break;
		}
		default: {
			$config = 'l-press::require_ssl';
		}
	}
	if(Config::get($config) && !Request::secure()) {
		if(!Config::get('l-press::ssl_is_sha2') || UserAgent::supportsSHA2()) {
			return Redirect::secure(Request::getRequestUri());
		}
		return Redirect::route('lpress-sha2');
	}
}
