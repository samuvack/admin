<?php

defined('APP_DEBUG') or define('APP_DEBUG', false);

return array(
	"debug" => APP_DEBUG,
	"application" => array(
		"name" => 'MyApplication',
		"version" => "0.1.0"
	),
	"mail" => array(
		'address' => 'you@yourdomain.com',
		'name' => 'Your Organization',
	)
);
