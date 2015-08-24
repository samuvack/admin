<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 15:51
 */

namespace MyApp\FormTypes;


class FormTypeProvider {
	private static $typeMap = null;

	private static function initTypeMap(){
		self::$typeMap = array(
			"text" => new TextType,
			"time" => new TimeType
		);
	}

	public static function getFormType($type){
		if(self::$typeMap === null)
			self::initTypeMap();

		if (! array_key_exists($type, self::$typeMap)) {
			$format = "No form for type '%s' found.";
			throw new \Exception(sprintf($format,$type));
		}

		return self::$typeMap[$type];
	}
}
