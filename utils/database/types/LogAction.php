<?php

namespace Utils\Database\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class LogAction extends Type {

	/**
	 * Gets the SQL declaration snippet for a field of this type.
	 *
	 * @param array $fieldDeclaration The field declaration.
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
	 *
	 * @return string
	 */
	private $map = [
		"delete"=>"D",
		"insert"=>"I",
		"update"=>"U"
	];
	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
		return "char";
	}

	public function convertToPHPValue($value, AbstractPlatform $platform) {
		foreach($this->map as $key=>$char)
			if($char == $value)
				return $key;

		return NULL;
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		return $this->map[$value];
	}

	/**
	 * Gets the name of this type.
	 *
	 * @return string
	 *
	 * @todo Needed?
	 */
	public function getName() {
		return "log_action";
	}
}
