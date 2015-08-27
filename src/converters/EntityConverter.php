<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/08/15
 * Time: 11:47
 */

namespace MyApp\Converters;


use Services\Mapping\TypeNotSupportedException;

class EntityConverter extends StringConverter {

	public function toString($object) {
		throw new TypeNotSupportedException("No direct DB conversion for entities supported yet.");
	}

	public function toObject($string) {
		throw new TypeNotSupportedException("No direct DB conversion for entities supported yet.");
	}
}
