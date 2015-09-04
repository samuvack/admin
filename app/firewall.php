<?php

$app['security.firewalls'] = array(
	'secured_area' => array(
		'pattern' => '^.*$',
		'anonymous' => true,
		'remember_me' => array(),
		'form' => array(
			'login_path' => '/user/login',
			'check_path' => '/user/login_check',
		),
		'logout' => array(
			'logout_path' => '/user/logout',
			'invalidate_session' => true
		),
		'users' => $app->share(function($app) { return $app['user.manager']; }),
	),
);

$app['security.role_hierarchy'] = array(
	'ROLE_ADMIN' => array('ROLE_EDITOR'),
	'ROLE_EDITOR' => array('ROLE_USER')
);

$app['security.access_rules'] = array(
	array('^/insert', 'ROLE_EDITOR'),
	array('^/import', 'ROLE_EDITOR'),
	array('^/update', 'ROLE_EDITOR'),
	array('^/property', 'ROLE_ADMIN')
);
