<?php

namespace MyApp\Entities\ShadowEntities;
class Filter {
	private $value;
	private $property;
	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public function setProperty($property){
		$this->property = $property;
	}

	public function getProperty() {
		return $this->property;
	}

	public function setType($type) {
		//ignore
	}

	public function getType(){
		if($this->property !== null)
			return $this->property->getDataType();
		return 'text';
	}

	public function filter($relations) {
		$result = [];
		foreach($relations as $relation) {
			if($this->value->filter($relation)){
				$result[] = $relation;
			}
		}
		return $result;
	}
}
