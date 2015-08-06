<?php
	require_once __DIR__.'/../vendor/autoload.php';

	require_once __DIR__.'/../src/NodeType.php';
	require_once __DIR__.'/../src/RelationType.php';
	require_once __DIR__.'/../src/FilterType.php';
	use Silex\Provider\DoctrineServiceProvider;
	use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
	use Doctrine\DBAL\Types\Type;


	$DEBUG = true; // TODO: move to config file
	if($DEBUG) {
		// include the php library for the Chrome logger to print variables to the Chrome Console
		include 'ChromePhp.php';
	}

	use Symfony\Component\Validator\Constraints as Assert;
	use Symfony\Component\Console\Question\ConfirmationQuestion;
	use Knp\Provider\ConsoleServiceProvider;
	use Silex\Provider;

	Class Application extends Silex\Application {
		use \Silex\Application\TwigTrait;
		use \Silex\Application\FormTrait;
		use \Silex\Application\SecurityTrait;
		use \Silex\Application\UrlGeneratorTrait;
	}

	$app = new Application();
	$app['debug'] = $DEBUG;


	$app->register(new ConsoleServiceProvider(), array(
		'console.name'              => 'MyApplication',
		'console.version'           => '1.0.0',
		'console.project_directory' => __DIR__.'/..'
	));

        $app->register(new DoctrineServiceProvider, array(
            "db.options" =>
                array( // TODO: Move to config file
                    'dbname' => 'Wikidata',
                    'user' => 'postgres',
                    'password' => 'postgres',
                    'host' => 'localhost',
                    'driver' => 'pdo_pgsql'
                )
            )
        );
        $app->register(new DoctrineOrmServiceProvider, array(
            "orm.em.options" => array(
                "mappings" => array(
                    array(
                        "type" => "annotation",
                        "namespace" => "MyApp\Entities",
                        "path" => __DIR__."/../src/entities/",
                        "alias" => ""
                    )
                ),
            ),
			"orm.custom.functions.string" => array(
				"plainto_tsquery" => "MyApp\Database\Functions\PlainToTsquery",
				"TS_MATCH_OP" => "MyApp\Database\Functions\TsMatch"
			)
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
	$app->register(new \Silex\Provider\TranslationServiceProvider(), array('translator.domains'=>array(),));

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
				'fromEmail' => array(
					// TODO: get from config file!
					'address' => 'you@yourdomain.com',
					'name' => 'Your Organization',
				),
			),
		)
	));

	$app->mount('/user', $userServiceProvider);
	require_once __DIR__ ."/firewall.php";

	Type::addType('tsvector', 'MyApp\Database\Types\Tsvector');

	$app->before(function($request) use($app) {
            $app['twig']->addGlobal('active',$request->get("_route"));
	});

	include __DIR__ . "/controllers/base.php"; //include controllers

return $app;
