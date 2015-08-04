<?php

namespace MyApp\DBConverters;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use MyApp\DBConverters\NodeConverter;

class DynamicType extends Type {

    /**
     * @var \Application
     */
    private $app;

   /* public function __construct(\Application $app){
        parent::__construct();
        $this->app = $app;
      //  print_r($app);die();
    }*/

    public function getName() {
        return "dynamicType";
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return "text";
    }
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value->getId();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $converter = new NodeConverter();
        $converter->test();

        return array("test"=>"x");
    }

}


