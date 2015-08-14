<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/08/15
 * Time: 09:28
 */

namespace MyApp\Converters;


class DateTimeConverter extends StringConverter {

	public function toString($object) {
		return $object->format(DateTime::ISO8601);
	}

	public function toObject($string) {
		return \DateTime::createFromFormat(\DateTime::ISO8601,$string);
	}
}
