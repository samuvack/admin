<?php

namespace MyApp\Entities\Listeners;
use MyApp\Entities\PropertyLog;

class PropertyLogging extends LoggingListener {
	protected function createLog($entity, $action) {
		return new PropertyLog($entity, $this->app['user'], $action);
	}
}
