<?php

namespace Utils\Services\Mapping;


class MappingManager {
	private $mappings = [];
	private $app;

	private $registerListeners = [];

	public function __construct(\Silex\Application $app){
		$this->app = $app;
	}

	public function register($type, $viewCallback, $dbCallback){
		$mapping = new Mapping($viewCallback, $dbCallback, $this->app);
		$this->mappings[$type] = $mapping;
		foreach($this->registerListeners as $listener) {
			$listener($type, $mapping);
		}
	}

	public function getDbConverter($type) {
		if(! isset($this->mappings[$type])) {
			$this->noMappingException($type);;
		}

		return $this->mappings[$type]->getDbConverter();
	}

	public function getFormType($datatype) {
		if(! isset($this->mappings[$datatype])) {
			$this->noMappingException($datatype);
		}

		return $this->mappings[$datatype]->getFormType();
	}

	public function toDbString($object, $type) {
		$this->getDbConverter($type)->toString($object);
	}

	public function fromDbString($string, $type) {
		$this->getDbConverter($type)->toObject($string);
	}

	public function onRegister($listener) {
		$this->registerListeners[] = $listener;
	}

	private function noMappingException($type) {
		$format = "Type %s is has no mapping.";
		throw new TypeNotSupportedException(sprintf($format,$type));
	}
}
