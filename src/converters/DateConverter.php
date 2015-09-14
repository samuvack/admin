<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/08/15
 * Time: 09:28
 */

namespace MyApp\Converters;


use MyApp\Values\DateValue;

class DateConverter extends StringConverter {

	public function toString($date) {
		return sprintf("%d/%d/%d", $date->getYear(), $date->getMonth(), $date->getDay());
	}

	public function toObject($string) {
		$arr = explode('/',$string);
		return new DateValue($arr[0], $arr[1], $arr[2]);
	}
}
