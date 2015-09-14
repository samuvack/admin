<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14/09/15
 * Time: 11:46
 */

namespace MyApp\Values;


use DateTime;
use MyApp\Entities\Relation;

class DateValue implements RenderableValue {
	private $year, $month, $day;

	public function __construct($year, $month, $day) {
		$this->year = $year;
		$this->month = $month;
		$this->day = $day;
	}

	public function getYear() {
		return $this->year;
	}

	public function getMonth() {
		return $this->month;
	}

	public function getDay() {
		return $this->day;
	}

	/**
	 * @return String simple string for use in e.g. the graph
	 */
	public function __toString() {
		$dateObj   = DateTime::createFromFormat('!m', $this->getMonth());
		$monthName = $dateObj->format('F');// Januari, Maart, etc
		return sprintf('%s %s %s', $this->getDay(), $monthName, $this->getYear());
	}

	/**
	 * Get FormType
	 */
	public function getFormType(\Silex\Application $app) {
		// TODO: Implement getFormType() method.
	}

	/**
	 * Extended view, for detailed representation
	 */
	public function render(\Twig_Environment $env, array $params) {
		$params = array_merge(array('date'=> $this), $params);
		$env->display("@values/node.twig", $params);
	}

	public function filter(Relation $relation) {
		$date2 = $relation->getValue();
		return $date2->getDay() === $this->getDay()
			&& $date2->getMonth() === $this->getMonth()
			&& $date2->getYear() === $this->getYear();
	}
}
