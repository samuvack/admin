<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\DBAL\Types\Type;
use Saxulum\DoctrineOrmManagerRegistry\Silex\Provider\DoctrineOrmManagerRegistryProvider;
use Silex\Provider\FormServiceProvider;

$config = include __DIR__ . "/config/main.php";

if ($config["debug"]) {
	// include the php library for the Chrome logger to print variables to the Chrome Console
	include 'ChromePhp.php';
}

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider;
use MyApp\Values\RenderableValue;

Class Application extends Silex\Application {
	use \Silex\Application\TwigTrait;
	use \Silex\Application\FormTrait;
	use \Silex\Application\SecurityTrait;
	use \Silex\Application\UrlGeneratorTrait;
}

$app = new Application();
$app['debug'] = $config["debug"];


$app->register(new ConsoleServiceProvider(), array(
	'console.name' => $config['application']['name'],
	'console.version' => $config['application']['version'],
	'console.project_directory' => __DIR__ . '/..'
));

$dbconfig = include __DIR__ . "/config/db.include.php";
$dbconfig["driver"] = 'pdo_pgsql';
$app->register(new DoctrineServiceProvider, array(
		"db.options" => $dbconfig
	)
);
$app->register(new DoctrineOrmServiceProvider, array(
	"orm.em.options" => array(
		"mappings" => array(
			array(
				"type" => "annotation",
				"namespace" => "MyApp\Entities",
				"path" => __DIR__ . "/../src/entities/",
				"alias" => ""
			)
		),
	),
	"orm.custom.functions.string" => array(
		"plainto_tsquery" => "MyApp\Database\Functions\PlainToTsquery",
		"TS_MATCH_OP" => "MyApp\Database\Functions\TsMatch"
	),
	'orm.auto_generate_proxies' => $app['debug']
));

$DB = new PDO('pgsql:
	host=localhost;
	dbname=Wikidata;
	user=postgres;
	password=postgres
');
$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/../views',));
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array('translator.domains' => array(),));

$app->register(new Provider\SecurityServiceProvider());
$app->register(new Provider\RememberMeServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider());

$userServiceProvider = new SimpleUser\UserServiceProvider();
$app->register($userServiceProvider, array(
	"user.options" => array(
		"userColumns" => array(
			'isEnabled' => 'is_enabled',
			'confirmationToken' => 'confirmation_token',
			'timePasswordResetRequested' => 'time_password_reset_requested'
		),
		'emailConfirmation' => array(
			// Only ask for mails in production mode
			'required' => ! $app['debug'],
		),
		'mailer' => array(
			'fromEmail' => $config['mail'],
			'enabled' => ! $app['debug']
		),
		'userRoles' => array(
			'ROLE_EDITOR', 'ROLE_ADMIN', 'ROLE_USER'
		),
		'templates' => array(
			'layout' => 'baselayout.twig'
		)
	)
));

$app->register(new FormServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());

$app->register(new Services\Mapping\MappingServiceProvider());
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
		return new \MyApp\FormTypes\NodeType($app);
	},
	new \MyApp\Converters\EntityConverter()
);

require_once __DIR__ . "/firewall.php";

$app->mount('/user', $userServiceProvider);

Type::addType('tsvector', 'MyApp\Database\Types\Tsvector');

$app->before(function ($request) use ($app) {
	$app['twig']->addGlobal('active', $request->get("_route"));
	$app['twig']->addFunction(new Twig_SimpleFunction('render', function (Twig_Environment $env, RenderableValue $value) {
		$value->render($env);
	}, array('needs_environment' => true)));
});

include __DIR__ . "/controllers/base.php"; //include controllers
include __DIR__ . "/controllers/ajax.php"; //include controllers

return $app;
