<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/08/15
 * Time: 09:00
 */

namespace Services\Mapping;


class Mapping {
	private $dbConverter;
	private $dbCB;

	private $viewCB;
	private $app;
	public function __construct($viewCB, $dbCB,\Silex\Application $app) {
		$this->app = $app;
		$this->dbCB = $dbCB;
		$this->viewCB = $viewCB;
	}

	public function getDbConverter(){
		if(!isset($this->dbConverter))
			$this->dbConverter = $this->instantiate($this->dbCB);
		return $this->dbConverter;
	}

	public function getFormType(){
		// return separate instance every time this gets called.
		return $this->instantiate($this->viewCB);
	}

	private function instantiate($cb) {
		if(is_callable($cb)) {
			return call_user_func($cb, $this->app);
		}

		return $cb;
	}
}
