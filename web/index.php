<?php
require_once __DIR__.'/../vendor/autoload.php';

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

$db = new PDO('pgsql:
	host=localhost;
	dbname=Wikidata;
	user=postgres;
	password=postgres
');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$app->register(new \Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__ . '/../views',));
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());
$app->register(new \Silex\Provider\TranslationServiceProvider(), array('translator.domains'=>array(),));
//$app->register(new \Silex\Provider\SessionServiceProvider());

$app->before(function($request) use($app) {
	$app['twig']->addGlobal('active',$request->get("_route"));
});

$app->match('/', function(Application $app, Request $request) use($db) {
	//create form
	$default = array(
		'name' =>''
	);
		
	$form = $app['form.factory']->createBuilder('form', $default)
		->add('name', 'search', array(
			'constraints' => array(new Assert\NotBlank()),
			'attr' => array('class'=>'form-control')
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
		
		//search in the database
		$query="
			SELECT * FROM nodes WHERE name =:term;
		";
	
		$stm = $db->prepare($query);
		$stm->execute(['term'=>$term]);
		$result = $stm->fetchAll();
	
		return $app['twig']->render('home.html', array('form'=>$form->createView(),'nodes'=>$result));
	}
	
	$query = "
		SELECT * FROM nodes
		ORDER BY id
	";
	$stm = $db->prepare($query);
	$stm->execute();
	$nodes = $stm->fetchAll();
	
	return $app['twig']->render('home.html', array('form'=>$form->createView(), 'nodes'=>$nodes));
	
})->bind('home');

$app->match('/insert', function(Request $request) use($app, $db) {
	//get the available properties (id, name)
	$queryProps = "
		SELECT id,name 
		FROM properties
	";
	
	$stm1 = $db->prepare($queryProps);
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
			
		$stm1 = $db->prepare($queryNode);
		$resultNode = $stm1->execute(['name'=>$data['name'],'description'=>$data['description']]);
		
		//determine id from newly inserted records
		$queryId = 'select id from nodes where name=:name and description=:description Limit 1';
		$stm2 = $db->prepare($queryId);
		$stm2->execute(['name'=>$data['name'],'description'=>$data['description']]);	
		$id = $stm2->fetch();
		
		//add the relation or property to the statements table
		$queryRelations = 'insert into statements(startID,propertyname,value) values(:start, :prop, :value)';
		$stm3 = $db->prepare($queryRelations);
		$resultRel = $stm3->execute(['start'=>$id[0], 'prop'=>$data['property'], 'value'=>$data['value']]);
		
		if($resultNode && $resultRel){
			return $app->redirect($app->path('home'));
		}
	}
		
	return $app['twig']->render('insert.html', array('form'=>$form->createView()));;
})->bind('insert');

$app->match('/update/{id}', function(Application $app, Request $request, $id) use($db) {
	//get the information for the given id
	$query= '
		SELECT * FROM nodes
		WHERE id = :id
	';
	
	$stm=$db->prepare($query);
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
	
		$stm = $db->prepare($query);
		$stm->execute(['name'=>$name, 'descr'=>$descr, 'id'=>$id]);
		$result = $stm->fetch();
	
		//redirect to home page
		return $app->redirect($app->path('home'));

	}
	
	//display the form
	return $app['twig']->render('update.html', array('form'=> $form->createView(), 'nummer'=>$id));
})->bind('update');

$app->get('/node/{id}', function(Application $app, $id) use($db) {
	//get node info
	$queryNode = "
		SELECT * FROM nodes
		WHERE id = :id
	";
	$stm1 = $db->prepare($queryNode);
	$stm1->execute(['id'=>$id]);
	$node = $stm1->fetch();
	
	//get relations starting from the node
	$queryRelFrom = "
		SELECT s.id as sid,p.id as pid,p.name as pname, p.datatype as ptype, s.value as svalue, n.name as nstart
		FROM statements as s, properties as p, nodes as n
		WHERE s.startID = :id and s.propertyName = p.id and s.startID = n.id
	";
				
	$stm2 = $db->prepare($queryRelFrom);
	$stm2->execute(['id'=>$id]);
	$relFrom = $stm2->fetchAll();
	
	//get relations with the node as value
	$queryRelTo = "	
		SELECT s.id as sid, p.id as pid, p.name as pname, s.startID as sstart, n.name as nstart, s.value as svalue, p.datatype as ptype 
		FROM statements as s, properties as p, nodes as n
		WHERE s.value = :id and s.propertyName = p.id and p.datatype='node' and n.id = s.startID;
	";
	$stm3 = $db->prepare($queryRelTo);
	$stm3->execute(['id'=>$id]);
	$relTo = $stm3->fetchAll();
	
	return $app['twig']->render('node.html', ['node'=>$node, 'relFrom'=>$relFrom, 'relTo'=>$relTo]);
})->bind('node');

$app->match('/search', function (Application $app, Request $request) use($db) {
	//create form
	$default = array(
		'search' =>''
	);
		
	$form = $app['form.factory']->createBuilder('form', $default)
		->add('search', 'search', array(
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
		$term = $data['search'];
		
		//search in the database
		$query="
			SELECT * FROM nodes WHERE descr@@plainto_tsquery('english',:term);
		";
	
		$stm = $db->prepare($query);
		$stm->execute(['term'=>$term]);
		$result = $stm->fetchAll();
	
		return $app['twig']->render('search.html', array('form'=>$form->createView(),'nodes'=>$result));
	}
	return $app['twig']->render('search.html', array('form'=>$form->createView(), 'nodes'=>[]));
})->bind('search');

$app->get('/map', function(Application $app) use ($db) {
	$query = "SELECT nodes.*, geometries_point.geom
				FROM nodes, statements, properties, geometries_point
				WHERE properties.datatype = 'geometry' and 
					properties.id = statements.propertyname and 
					nodes.id = statements.startID and 
					statements.value::integer = geometries_point.id
				";
		
	$stm = $db->prepare($query);
	$stm->execute();
	$geonodes = $stm->fetchAll();
	
	return $app['twig']->render('map.html', ['nodes'=>$geonodes]);
})->bind('map');

$app->get('/history/{id}', function(Application $app, $id) use ($db) {
	//get node info
	$queryNode = "
		SELECT * FROM nodes
		WHERE id = :id
	";
	$stm1 = $db->prepare($queryNode);
	$stm1->execute(['id'=>$id]);
	$node = $stm1->fetch();

	$queryHistory = "SELECT * 
						FROM nodes_logging
						WHERE id = :id
						ORDER BY action_time
	";
	$stm2 = $db->prepare($queryHistory);
	$stm2->execute(['id'=>$id]);
	$history = $stm2->fetchAll();
	
	return $app['twig']->render('history.html', ['node'=>$node, 'edits'=>$history]);
})->bind('history');

$app->run();

?>