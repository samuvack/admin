<?php

namespace MyApp\Converters;


use MyApp\Values\YearPeriodValue;

class YearPeriodConverter extends StringConverter {
	private static $chars = [
		'/', '-', '\\', ';'
	];

	public function toString($object) {
		return $object->getStartyear() ."/".$object->getEndyear();
	}

	public function toObject($string) {
		foreach($this::$chars as $char) {
			$exploded = explode($char, $string, 2);
			if(sizeof($exploded) > 1 )
				return new YearPeriodValue($exploded[0], $exploded[1]);
		}

		return new YearPeriodValue($string, $string);
	}
}
