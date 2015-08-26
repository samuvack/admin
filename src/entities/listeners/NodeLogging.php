<?php
namespace MyApp\Entities\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use MyApp\Entities\NodeLog;
use Silex\Application;
use MyApp\Entities\Node;

class NodeLogging extends LoggingListener {
	protected function createLog($entity, $action) {
		return new NodeLog($entity, $this->app['user'], $action);
	}
}
