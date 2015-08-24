<?php

namespace MyApp\Converters;


abstract class StringConverter {
    static $converterMap = null;

    private static function initConverterMap(){
        self::$converterMap = array(
            "text" => new TextConverter,
            "dateTime" => new DateTimeConverter
        );
    }

    public static function getConverter($type){
        if(self::$converterMap === null)
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
