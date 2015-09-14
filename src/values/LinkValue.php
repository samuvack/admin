<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 14/09/15
 * Time: 14:24
 */

namespace MyApp\Values;


use MyApp\FormTypes\TextType;

class LinkValue {
	private $url;

	public function __construct($url) {
		if(! is_string($url) ) {
			throw new \Exception(sprintf("Type %s is not a string.", gettype($url)));
		}
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}

	public function setUrl($url) {
		if(! is_string($url) ) {
			throw new \Exception(sprintf("Type %s is not a string.", gettype($url)));
		}
		$this->url = $url;
	}

	/**
	 * @return String simple string for use in e.g. a graph
	 */
	public function __toString() {
		return $this->url;
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
		$params = array_merge(array('url'=> $this), $params);
		$env->display("@values/url.twig", $params);
	}

	public function filter(Relation $relation) {
		return $relation->getValue()->__toString() == $this->__toString();
	}
}
