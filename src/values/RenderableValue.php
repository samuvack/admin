<?php

namespace MyApp\Values;


interface RenderableValue {
	/**
	 * @return String simple string for use in e.g. the graph
	 */
	public function __toString();

	/**
	 * Get FormType
	 */
	public function getFormType(\Silex\Application $app);

	/**
	 * Extended view, for detailed representation
	 */
	public function render(\Twig_Environment $env, array $params);
}
