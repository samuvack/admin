<?php

namespace MyApp\DBConverters;

use Silex\Application;
use Silex\ServiceProviderInterface;

class NodeConverter implements ServiceProviderInterface {

    /**
     * @var Application
     */
    private $app;

    public function register(Application $app){
        $this->app = $app;
        //print_r($app['orm.em']);die();
    }

    public function boot(Application $app) {

    }

    public function index(Silex\Application $app) {
        $this->app = $app;
        die();
    }

    public function getName() {
        return "node";
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return "text";
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value->getId();
    }

    public function test(){
        print_r($this->app);
        die();
    }
}
