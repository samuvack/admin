<?php

defined('APP_DEBUG') or define('APP_DEBUG', false);

return array(
	/* DEBUG mode:
	 * 	No mails are sent.
	 * 	Verbose exceptions
	 * 	Doctrine proxies are auto-generated
	 */
	"debug" => APP_DEBUG,
	"application" => array(
		"name" => 'MyApplication',
		"version" => "0.1.0"
	),
	"mail" => array(
		'address' => 'you@yourdomain.com',
		'name' => 'Your Organization',
	),
	"pagination" => array(
		'nodes_per_page'=>10
	),
	'importFileDir' => __DIR__ . "/../../files/",
	"baseUrl" => "http://crest.ugent.be"
);
