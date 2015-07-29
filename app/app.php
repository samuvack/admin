<?php
	require_once __DIR__.'/../vendor/autoload.php';
	require_once __DIR__.'/../src/node.php';
	require_once __DIR__.'/../src/property.php';
	require_once __DIR__.'/../src/relation.php';
	require_once __DIR__.'/../src/NodeType.php';
	require_once __DIR__.'/../src/RelationType.php';
	require_once __DIR__.'/../src/FilterType.php';
	//include the php library for the Chrome logger to print variables to the Chrome Console
	include 'ChromePhp.php';

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Validator\Constraints as Assert;
	use Silex\Privider\FormServiceProvider;

	Class Application extends Silex\Application {
		use \Silex\Application\TwigTrait;
		use \Silex\Application\FormTrait;
		use \Silex\Application\SecurityTrait;
		use \Silex\Application\UrlGeneratorTrait;
	}

	$app = new Application();
	$app['debug'] = true;

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
	//$app->register(new \Silex\Provider\SessionServiceProvider());

	
	
	$app->before(function($request) use($app) {
		$app['twig']->addGlobal('active',$request->get("_route"));
	});

	$app->match('/', function(Application $app, Request $request) use($DB) {
		//create form
		$default = array(
			'name' =>''
		);
		$form = $app['form.factory']->createBuilder('form', $default)
			->add('name', 'search', array(
				'constraints' => array(new Assert\NotBlank()),
				'attr' => array('class'=>'form-control', 'id'=>'form_search')
			))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default')
			))
			->getForm();
		$form->handleRequest($request);
		
		//check form
		if ($form->isValid()) {
			//get the search term
			$data = $form->getData();
			$term = $data['name'];
			$result = Node::findByName($term);
		
			return $app['twig']->render('home.html', array('form'=>$form->createView(),'nodes'=>$result));
		}
		
		//use the getAll function of the Node class to gather all the nodes
		$nodes = Node::getAll();
		return $app['twig']->render('home.html', array('form'=>$form->createView(), 'nodes'=>$nodes));
		
	})->bind('home');

	$app->match('/insert', function(Request $request) use($app, $DB) {

		//default data for the form to be displayed
		$node = new Node(null, '', '', null);
		$relation1 = new Relation(null, null, null, null, null, null);
		$node->addRelation($relation1);			
		
		//generate the form
		$form = $app['form.factory']->createBuilder(new NodeType(), $node)->getForm();
				
		$form->handleRequest($request);
		
		if($form->isValid()) {
			$data=$form->getData();
			$node->save();
			
			return $app->redirect($app->path('home'));
			
		}
			
		return $app['twig']->render('insert.html', array('form'=>$form->createView()));;
	})->bind('insert');

	$app->get('/node/{id}', function(Application $app, $id) use($DB) {
		//get node info
		$node = Node::findById($id);
		//get relations from and to this node
		$relFrom = $node->findRelations();
		$relTo = $node->findEndRelations();
		
		return $app['twig']->render('node.html', ['node'=>$node, 'relFrom'=>$relFrom, 'relTo'=>$relTo]);
	})->bind('node');	
	
	$app->match('/update/{id}', function(Application $app, Request $request, $id) use($DB) {
		//get the node information for the given id	
		$node = Node::findById($id);
		
		//store all available relations in the relations property of the node
		
		
		$form = $app['form.factory']->createBuilder(new NodeType(), $node)->getForm();
		$form->handleRequest($request);
		
		//check form
		if ($form->isValid()) {
			$node->update($node->getName(), $node->getDescription());
			//update the relations
			//insert newly added relations
			
			return $app->redirect($app->path('home'));
		}
		
		//display the form
		return $app['twig']->render('update.html', array('form'=> $form->createView(), 'nodeid'=>$id));
	})->bind('update');

	$app->match('/search', function (Application $app, Request $request) use($DB) {
		//create form
		$default = array(
			'search' =>''
		);
			
		$form = $app['form.factory']->createBuilder('form', $default)
			->add('description', 'search', array(
				'constraints' => array(new Assert\NotBlank()),
				'attr' => array('class'=>'form-control')
			))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default'), 'label'=>'Start searching'
			))
			->getForm();
		$form->handleRequest($request);
		
		//check form
		if ($form->isValid()) {
			//get the search term
			$data = $form->getData();
			$term = $data['description'];
			
			//search in the database
			$result = Node::findByDescription($term);
		
			return $app['twig']->render('search.html', array('form'=>$form->createView(),'nodes'=>$result));
		}
		return $app['twig']->render('search.html', array('form'=>$form->createView(), 'nodes'=>[]));
	})->bind('search');

	$app->get('/map', function(Application $app) use ($DB) {
		$geonodes = Node::getAllGeoNodes();
		
		return $app['twig']->render('map.html', ['nodes'=>$geonodes]);
	})->bind('map');

	$app->get('/history/{id}', function(Application $app, $id) use ($DB) {
		//get node info
		$node = Node::findById($id);
		$history = $node->findHistory();
		
		return $app['twig']->render('history.html', ['node'=>$node, 'edits'=>$history]);
	})->bind('history');

	$app->match('/filter', function(Application $app, Request $request) use($DB) {
		//create form with default data
		$default = array(
			'type'=>'',
			'property'=>'',
			'value' =>''
		);	
		
		$form = $app['form.factory']->createBuilder(new FilterType(), $default)->getForm();
		$form->handleRequest($request);
		
		if ($form->isValid()) {			
			//get the property id and value
			$data = $form->getData();
			$id = $data['property'];
			$value = $data['value'];
			
			//get the nodes with this property and value
			$nodes = Node::findByPropertyValue($id, $value);
			
			return $app['twig']->render('filter.html', array('form'=>$form->createView(), 'nodes'=>$nodes));
		}
		return $app['twig']->render('filter.html', array('form'=>$form->createView(), 'nodes'=>array()));
	})->bind('filter');

	return $app;
?>