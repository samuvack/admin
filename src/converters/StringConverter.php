<?php

namespace MyApp\Converters;
use Utils\Services\Mapping\TypeNotSupportedException;

abstract class StringConverter {
    private static $converterMap = [];


    public static function addConverter($type, $converter) {
        self::$converterMap[$type] = $converter;
    }

    public static function getConverter($type){
        if (! array_key_exists($type, self::$converterMap)) {
            $format = "No Converter for type '%s' found.";
            throw new TypeNotSupportedException(sprintf($format,$type));
        }

        return self::$converterMap[$type];
    }

    public static function getDataTypes() {
        return array_keys(self::$converterMap);
    }

    public abstract function toString($object);
    public abstract function toObject($string);
}
