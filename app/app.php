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
$app['config'] = $config;

// Service for terminal commands
$app->register(new ConsoleServiceProvider(), array(
	'console.name' => $config['application']['name'],
	'console.version' => $config['application']['version'],
	'console.project_directory' => __DIR__ . '/..'
));

// Import Database config, and start Doctrine service
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
	// Postgis functions
	"orm.custom.functions.string" => array(
		"plainto_tsquery" => "Utils\Database\Functions\PlainToTsquery",
		"TS_MATCH_OP" => "Utils\Database\Functions\TsMatch"
	),
	'orm.auto_generate_proxies' => $app['debug']
));

// Postgis and custom types
Type::addType('log_action', 'Utils\Database\Types\LogAction');

$app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' =>array(
	'default'=>__DIR__ . '/../views'
)));
$app['twig.loader.filesystem']->addPath($app['twig.loader.filesystem']->getPaths()[0].'/values', 'values');
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array('translator.domains' => array(),));

$app->register(new Provider\SecurityServiceProvider());
$app->register(new Provider\RememberMeServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider());

$userServiceProvider = new MyApp\User\UserServiceProvider();
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
			'layout' => 'baselayout.twig',
			'view' => 'user/profile.twig'
		),
		'userClass' => 'MyApp\Entities\User'
	)
));

$app->mount('/user', $userServiceProvider);
// More config for user auth system
require_once __DIR__ . "/firewall.php";

$app->register(new FormServiceProvider());
$app->register(new DoctrineOrmManagerRegistryProvider());


include __DIR__.'/common_app.php';

$listener = new \MyApp\Entities\Listeners\NodeLogging($app);
$app['orm.em']->getConfiguration()->getEntityListenerResolver()->register($listener);
$listener = new \MyApp\Entities\Listeners\RelationLogging($app);
$app['orm.em']->getConfiguration()->getEntityListenerResolver()->register($listener);
$listener = new \MyApp\Entities\Listeners\PropertyLogging($app);
$app['orm.em']->getConfiguration()->getEntityListenerResolver()->register($listener);

include __DIR__ . "/controllers/base.php"; //include controllers
include __DIR__ . "/controllers/ajax.php"; //include controllers
$app->mount('import', include 'controllers/import.php');
return $app;
