<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 10:03
 */

namespace MyApp\Values;


class TextValue implements RenderableValue {
	private $text;

	public function __construct($text) {
		$this->text = $text;
	}

	public function getText() {
		return $this->text;
	}

	/**
	 * @return String simple string for use in e.g. a graph
	 */
	public function __toString() {
		return $this->text;
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
	public function render() {
		echo $this->text;
	}
}
