<?php
use Doctrine\DBAL\Types\Type;
use MyApp\Values\RenderableValue;
use Symfony\Component\HttpFoundation\Request;

Type::addType('tsvector', 'Utils\Database\Types\Tsvector');
Type::addType('geometry', 'Utils\Database\Types\Geometry');

$app->register(new Utils\Services\Mapping\MappingServiceProvider());
$app['mapping.manager']->onRegister(function($type, $mapping) {
	\MyApp\Converters\StringConverter::addConverter($type, $mapping->getDbConverter());
});
$app['mapping.manager']->register('text',
	function($app){
		return new \MyApp\FormTypes\TextType();
	},
	new \MyApp\Converters\TextConverter()
);
$app['mapping.manager']->register('year_period',
	function($app){
		return new \MyApp\FormTypes\YearPeriodType();
	},
	new \MyApp\Converters\YearPeriodConverter()
);
$app['mapping.manager']->register('node',
	function($app){
		return new \MyApp\FormTypes\NodeType($app, true);
	},
	new \MyApp\Converters\EntityConverter()
);
$app['mapping.manager']->register('geometry',
	function($app){
		return new \MyApp\FormTypes\GeometryType($app, true);
	},
	new \MyApp\Converters\EntityConverter()
);

$app->before(function (Request $request) use ($app) {
	$app['twig']->addGlobal('active', $request->get("_route"));
	$app['twig']->addFunction(
		new Twig_SimpleFunction('render',
			function (Twig_Environment $env, RenderableValue $value, array $params = array()) {
				$value->render($env, $params);
			},
			array('needs_environment' => true)
		)
	);
});
