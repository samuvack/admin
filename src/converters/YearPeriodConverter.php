<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 16:12
 */

namespace MyApp\Converters;


use MyApp\Values\YearPeriodValue;

class YearPeriodConverter extends StringConverter {

	public function toString($object) {
		return $object->getStartyear() ."/".$object->getEndyear();
	}

	public function toObject($string) {
		$exploded = explode('/', $string, 2);
		return new YearPeriodValue($exploded[0], $exploded[1]);
	}
}
