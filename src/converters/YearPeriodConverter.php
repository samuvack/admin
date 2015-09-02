<?php

namespace MyApp\Converters;


use MyApp\Values\YearPeriodValue;

class YearPeriodConverter extends StringConverter {
	private static $chars = [
		'/','\\', ';', '-'
	];

	public function toString($object) {
		return $object->getStartyear() ."/".$object->getEndyear();
	}

	public function toObject($string) {
		foreach($this::$chars as $char) {
			$exploded = explode($char, $string, 2);
			if(sizeof($exploded) > 1 ) {
				foreach($exploded as $key=>$value) {
					$exploded[$key] = intval(preg_replace("/[^0-9]/", "", $value));
					if(strpos($value, 'BC'))
						$exploded[$key] = -$exploded[$key];
				}
				return new YearPeriodValue($exploded[0], $exploded[1]);
			}
		}

		return new YearPeriodValue($string, $string);
	}
}
