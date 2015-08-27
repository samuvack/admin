<?php

namespace MyApp\Converters;

use Silex\Application;
use MyApp\Values\TextValue;

class TextConverter extends StringConverter {


    public function toString($object) {
        if($object == null) {
            return "";
        }
        return $object->getText();
    }

    public function toObject($string) {
       return new TextValue($string);
    }
}
