<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\HTML;

HTML::macro('asset', function($type, $path, $attributes = array()) {
	$asset_domain = Config::get('lpress::settings.asset_domain');
	$asset_domain = empty($asset_domain) ? DOMAIN : $asset_domain;
	$route_prefix = (new PrefixGenerator)->getPrefix();
	$open = '';
	$close = '';
	switch($type) {
		case 'css': {
			$path = "css/" . PRODUCTION . '/' . $path;
			$open .= "<link rel='stylesheet' type='text/css' href='//" . $asset_domain . $route_prefix ."/+assets/" . $path . "?v";
			$close .= "'>";
			break;
		}
		case 'js': {
			$path = "js/" . PRODUCTION . '/' . $path;
			$open .= "<script type='text/javascript' src='//" . $asset_domain . $route_prefix . "/+assets/" . $path . "?v";
			$close .= "'></script>";
			break;
		}
		case 'img': {
			$open .= "<img src='//" . $asset_domain . $route_prefix . "/+assets" . $path . "?v";
			$close .= "'" . MacroLoader::getAttributeString($attributes) . "/>";
			break;
		}
		default: {
			break;
		}
	}
	$version = '';
	$version = @filemtime(AssetController::getAssetPath() . '/' . $path);
	if($version == '') {
		$close = "' data-err='$path could not be found" . $close;
	}
	return $open . $version . $close;
});
