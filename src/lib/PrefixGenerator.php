<?php namespace EternalSword\LPress;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class PrefixGenerator {
	public $type;

	public function __construct($type = 'route') {
		$this->type = $type;
	}

	protected function getRoutePrefix() {
		$route_prefix = Config::get('l-press::route_prefix');
		$route_prefix = $route_prefix == '/' ? '' : $route_prefix;
		return $route_prefix;
	}

	protected function getDashboardPrefix() {
		$route_prefix = $this->getRoutePrefix();
		$route_prefix = empty($route_prefix) ? '/' : $route_prefix;
		$url_prefix = rtrim('//' . DOMAIN . '/' . $route_prefix, '/');
		$dashboard_route = '+' . Config::get('l-press::dashboard_route');
		return $url_prefix . '/' . $dashboard_route;
	}

	public function setType($type) {
		$this->type = $type;
	}

	public function getPrefix() {
		$prefix_method = 'get' . Str::title($this->type) . 'Prefix';
		if(is_callable(array($this, $prefix_method))) {
			return $this->$prefix_method();
		}
	}
}
