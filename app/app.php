<?php
	require_once __DIR__.'/../vendor/autoload.php';
	require_once __DIR__.'/../src/node.php';
	require_once __DIR__.'/../src/property.php';
	require_once __DIR__.'/../src/relation.php';

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
		//get the available properties (id, name)
		$queryProps = "
			SELECT id,name 
			FROM properties
		";
		
		$stm1 = $DB->prepare($queryProps);
		$stm1->execute();
		$properties = $stm1->fetchAll();
		
		//store the properties in an array format id=>name for the choice form field
		$options = array();
		foreach($properties as $p){
			$options[$p['id']]=$p['name'];
		}

		$default = array(
			'name' =>'',
			'description'=>''
		);

		$form = $app['form.factory']->createBuilder('form', $default)
			->add('name','text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The name of the item')
			))
			->add('description', 'textarea', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The description of the item')
			))
			->add('property', 'choice', array(
				'choices'=>$options,
				'attr'=>array('class'=>'form-control','placeholder'=>'The property for the item')
			))
			->add('value', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The value for the property or relation')
			))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default')
			))
			->getForm();
				
		$form->handleRequest($request);
		
		if($form->isValid()) {
		
			$data=$form->getData();
			
			//add the node to the db
			$queryNode = 'insert into nodes(name, description) values(:name, :description)';
				
			$stm1 = $DB->prepare($queryNode);
			$resultNode = $stm1->execute(['name'=>$data['name'],'description'=>$data['description']]);
			
			//determine id from newly inserted records
			$queryId = 'select id from nodes where name=:name and description=:description Limit 1';
			$stm2 = $DB->prepare($queryId);
			$stm2->execute(['name'=>$data['name'],'description'=>$data['description']]);	
			$id = $stm2->fetch();
			
			//add the relation or property to the statements table
			$queryRelations = 'insert into statements(startID,propertyname,value) values(:start, :prop, :value)';
			$stm3 = $DB->prepare($queryRelations);
			$resultRel = $stm3->execute(['start'=>$id[0], 'prop'=>$data['property'], 'value'=>$data['value']]);
			
			if($resultNode && $resultRel){
				return $app->redirect($app->path('home'));
			}
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
		//get the information for the given id
		$query= '
			SELECT * FROM nodes
			WHERE id = :id
		';
		
		$stm=$DB->prepare($query);
		$stm->execute(['id'=>$id]);
		$node= $stm->fetch();
		
		//default form values
		$data = array(
			'name' => $node['name'],
			'description' => $node['description']
		);
		
		//create form
		$form = $app['form.factory']->createBuilder('form', $data)
			->add('name', 'text', array(
				'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control')
			))
			->add('description', 'textarea', array(
				'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control')
			))
			->add('send', 'submit', array(
				'attr' => array('class'=>'btn btn-default')
			))
			->getForm();
		$form->handleRequest($request);
		//check form
		if ($form->isValid()) {
			$data = $form->getData();
			
			//update the record in the db
			$name = $data['name'];
			$descr = $data['description'];
		
			$query='
				UPDATE nodes SET name= :name, description= :descr
				WHERE id= :id
			';
		
			$stm = $DB->prepare($query);
			$stm->execute(['name'=>$name, 'descr'=>$descr, 'id'=>$id]);
			$result = $stm->fetch();
		
			//redirect to home page
			return $app->redirect($app->path('home'));

		}
		
		//display the form
		return $app['twig']->render('update.html', array('form'=> $form->createView(), 'nummer'=>$id));
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
			$query="
				SELECT * FROM nodes WHERE descr@@plainto_tsquery('english',:term);
			";
		
			$stm = $DB->prepare($query);
			$stm->execute(['term'=>$term]);
			$result = $stm->fetchAll();
		
			return $app['twig']->render('search.html', array('form'=>$form->createView(),'nodes'=>$result));
		}
		return $app['twig']->render('search.html', array('form'=>$form->createView(), 'nodes'=>[]));
	})->bind('search');

	$app->get('/map', function(Application $app) use ($DB) {
		$query = "SELECT nodes.*, geometries.geom
					FROM nodes, statements, properties, geometries
					WHERE properties.datatype = 'geometry' and 
						properties.id = statements.propertyname and 
						nodes.id = statements.startID and 
						statements.value::integer = geometries.id
					";
			
		$stm = $DB->prepare($query);
		$stm->execute();
		$geonodes = $stm->fetchAll();
		
		return $app['twig']->render('map.html', ['nodes'=>$geonodes]);
	})->bind('map');

	$app->get('/history/{id}', function(Application $app, $id) use ($DB) {
		//get node info
		$queryNode = "
			SELECT * FROM nodes
			WHERE id = :id
		";
		$stm1 = $DB->prepare($queryNode);
		$stm1->execute(['id'=>$id]);
		$node = $stm1->fetch();

		$queryHistory = "SELECT * 
							FROM nodes_logging
							WHERE id = :id
							ORDER BY action_time
		";
		$stm2 = $DB->prepare($queryHistory);
		$stm2->execute(['id'=>$id]);
		$history = $stm2->fetchAll();
		
		return $app['twig']->render('history.html', ['node'=>$node, 'edits'=>$history]);
	})->bind('history');

	$app->match('/filter', function(Application $app, Request $request) use($DB) {
		//create form
		$default = array(
			'property'=>'',
			'value' =>''
		);	
		
		//store the fitler types in an array format id=>name for the choice form field
		$options = array(
			'1'=>'time',
			'2'=>'geometry',
			'3'=>'other'
		);
		
		//query for the properties based on type (variable $selectedType)
		$selectedType = null;
		$queryProp = "
			SELECT id, name 
			FROM properties
			WHERE id = :type
		";
		$stm1 = $DB->prepare($queryProp);
		$stm1->execute(['type'=>$selectedType]);
		$prop = $stm1->fetchAll();
		
		//store the properties in an array format id=>name for the choice form field
		$properties = array();
		foreach($prop as $p){
			$properties[$p['id']]=$p['name'];
		}
		
		//first form containing dropdown to choose the type of filtering
		//the available properties for that type should be loaded in the property field
		$form = $app['form.factory']->createBuilder('form', $default)		
			->add('type', 'choice', array(
				'choices'=>$options,
				'attr'=>array('class'=>'form-control','placeholder'=>'The filter type'),
				'label'=>'Filter on'
			))
			->add('property', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The filter property or relation'),
				'label'=>'Where node has property or relation:'
			))
			->add('value', 'text', array(
				'constraints'=>array(new Assert\NotBlank(),new Assert\Length(array('min'=>3))),
				'attr' => array('class'=>'form-control', 'placeholder'=>'The filter value'),
				'label'=>'With value:'
			))
			->getForm();
		$form->handleRequest($request);
		
		if ($form->isValid()) {
			//ADD form handling//
			return $app['twig']->render('filter.html', array('form'=>$form->createView()));
			
		}
		return $app['twig']->render('filter.html', array('form'=>$form->createView()));
	})->bind('filter');

	return $app;
?>