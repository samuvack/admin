<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 16:12
 */

namespace MyApp\Converters;


class TimeConverter extends StringConverter {

	public function toString($object) {
		return $object;
	}

	public function toObject($string) {
		return $string;
	}
}
