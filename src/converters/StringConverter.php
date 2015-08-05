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
        if(self::$converterMap !== null)
            self::initConverterMap();

    }

    public abstract function toString($object);
    public abstract function toObject($string);
}
