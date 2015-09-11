<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/08/15
 * Time: 10:03
 */

namespace MyApp\Values;


use MyApp\Entities\Relation;
use MyApp\FormTypes\TextType;

class TextValue implements RenderableValue {
	private $text;

	public function __construct($text) {
		if(! is_string($text) ) {
			throw new \Exception(sprintf("Type %s is not a string.", gettype($text)));
		}
		$this->text = $text;
	}

	public function getText() {
		return $this->text;
	}

	public function setText($text) {
		if(! is_string($text) ) {
			throw new \Exception(sprintf("Type %s is not a string.", gettype($text)));
		}
		$this->text = $text;
	}

	/**
	 * @return String simple string for use in e.g. a graph
	 */
	public function __toString() {
		return $this->getText();
	}

	/**
	 * Get FormType
	 */
	public function getFormType(\Silex\Application $app) {
		return new TextType($this);
	}

	/**
	 * Extended view, for detailed representation
	 */
	public function render(\Twig_Environment $env, array $params) {
		$params = array_merge(array('text'=> $this, 'plain'=> false), $params);
		$env->display("@values/text.twig", $params);
	}

	public function filter(Relation $relation) {
		return $relation->getValue()->__toString() == $this->__toString();
	}
}
