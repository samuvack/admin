<?php

namespace MyApp\Entities\Listeners;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Silex\Application;

abstract class LoggingListener {
	protected $app;
	protected $logs = [];

	public function __construct(Application $app) {
		$this->app = $app;
	}

	protected abstract function createLog($entity, $action);

	public function prePersist($node, LifecycleEventArgs $event) {
		$event->getEntityManager()->persist($this->createLog($node,'insert'));
	}

	public function preUpdate($node, LifecycleEventArgs $event) {
		$event->getEntityManager()->persist($this->createLog($node,'update'));
	}

	public function preRemove($node, LifecycleEventArgs $event) {
		$event->getEntityManager()->persist($this->createLog($node,'delete'));
	}
}
