<?php

use Silex\Provider;

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
		),
		'users' => $app->share(function($app) { return $app['user.manager']; }),
	),
);
