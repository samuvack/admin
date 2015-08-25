<?php

namespace MyApp\Converters;
use Utils\Services\Mapping\TypeNotSupportedException;

abstract class StringConverter {
    static $converterMap = null;

    private static function initConverterMap(){
        if(self::$converterMap === null)
            self::$converterMap = array(
               // "text" => new TextConverter,
                "dateTime" => new DateTimeConverter
            );
    }

    public static function addConverter($type, $converter) {
        self::initConverterMap();
        self::$converterMap[$type] = $converter;
    }

    public static function getConverter($type){
        self::initConverterMap();

        if (! array_key_exists($type, self::$converterMap)) {
            $format = "No Converter for type '%s' found.";
            throw new TypeNotSupportedException(sprintf($format,$type));
        }

        return self::$converterMap[$type];
    }

    public abstract function toString($object);
    public abstract function toObject($string);
}
