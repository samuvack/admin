<?php

namespace MyApp\Converters;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TextConverter extends StringConverter {


    public function toString($object) {
        return $object;
    }

    public function toObject($string) {
       return $string;
    }
}
