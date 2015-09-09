<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/08/15
 * Time: 10:34
 */

namespace MyApp\Values;


use MyApp\Entities\Relation;
use MyApp\FormTypes\YearPeriodType;

class YearPeriodValue implements RenderableValue {
	private $start;
	private $end;
	public function __construct($start, $end) {
		$this->start = $start;
		$this->end = $end;
	}

	public function getStartyear() {
		return $this->start;
	}

	public function getEndyear() {
		return $this->end;
	}

	public function setStartyear($start) {
		$this->start = $start;
	}

	public function setEndyear($end) {
		$this->end = $end;
	}

	/**
	 * @return String simple string for use in e.g. the graph
	 */
	public function __toString() {
		return $this->start . " - " . $this->end;
	}

	/**
	 * Get FormType
	 */
	public function getFormType(\Silex\Application $app) {
		return new YearPeriodType($this);
	}

	/**
	 * Extended view, for detailed representation
	 */
	public function render(\Twig_Environment $env, array $params) {
		// TODO: Implement render() method.
	}

	/*
	 * Returns true if the two periods overlap (=atleast 1 common year).
	 */
	public function filter(Relation $relation) {

		$value = $relation->getValue();
		return $this->start <= $value->getEndyear() && $value->getStartyear() <= $this->end;
	}
}
