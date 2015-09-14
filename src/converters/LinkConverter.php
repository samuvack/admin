<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14/09/15
 * Time: 14:24
 */

namespace MyApp\Converters;


use MyApp\Values\LinkValue;

class LinkConverter extends StringConverter {
	public function toString($object) {
		if($object == null) {
			return "";
		}
		return $object->getUrl();
	}

	public function toObject($string) {
		return new LinkValue($string);
	}
}
