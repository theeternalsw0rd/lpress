<?php
	return array(
		'attachments' => array('path_base' => 'package', 'path' => 'attachments'),
		'themes' => array('path_base' => 'package', 'path' => 'resources/views/themes'),
		'route_index' => array(
			'controller' => 'EternalSword\Controllers\IndexController',
			'action' => 'getIndex'
		),
		'db_prefix' => 'lpress_',
		'dashboard_route' => 'dashboard',
		'dashboard_require_ssl' => TRUE,
		'login_require_ssl' => TRUE,
		'require_ssl' => FALSE,
		'route_prefix' => '/', // must include trailing slash
		'asset_domain' => '', // will use the same domain as the originating request by default
		'ssl_is_sha2' => FALSE, // set to TRUE if SSL certificates use SHA2 
		'available_languages' => array('en')
	);
