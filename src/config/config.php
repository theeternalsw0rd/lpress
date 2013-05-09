<?php
	return array(
		'uploads' => array('path_base' => 'package', 'path' => 'uploads'),
		'themes' => array('path_base' => 'package', 'path' => 'views/themes'),
		'route_index' => array(
			'controller' => 'EternalSword\LPress\IndexController',
			'action' => 'getIndex'
		),
		'db_prefix' => 'lpress_',
		'route_prefix' => '/' // must include trailing slash
	);
