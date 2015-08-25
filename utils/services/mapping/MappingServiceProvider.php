<?php
namespace Utils\Services\Mapping;
use Silex\ServiceProviderInterface;
class MappingServiceProvider implements ServiceProviderInterface {

	/**
	 * Registers services on the given app.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 */
	public function register(\Silex\Application $app) {
		$app['mapping.manager'] = new MappingManager($app);
	}

	/**
	 * Bootstraps the application.
	 *
	 * This method is called after all services are registered
	 * and should be used for "dynamic" configuration (whenever
	 * a service must be requested).
	 */
	public function boot(\Silex\Application $app) {
		// TODO: Implement boot() method.
	}
}
