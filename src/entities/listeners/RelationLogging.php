<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 26/08/15
 * Time: 10:48
 */

namespace MyApp\Entities\Listeners;


use MyApp\Entities\RelationLog;

class RelationLogging extends LoggingListener {

	protected function createLog($entity, $action) {
		return new RelationLog($entity, $this->app['user'], $action);
	}
}
