<?php
require_once __DIR__.'/../../vendor/autoload.php';
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use MyApp\Entities\Node;
use MyApp\FormTypes\FilterType;
use MyApp\FormTypes\NodeType;
use MyApp\Entities\Property;
use MyApp\FormTypes\PropertyType;;
use JasonGrimes\Paginator;
use Utils\Graph\GraphData;

require_once __DIR__.'/../ChromePhp.php';
$app->match('/', function(Application $app){
	return $app->redirect($app->path('home'));
});

$app->match('/home/{page}/{term}', function(Application $app, Request $request, $page, $term) {

	$nodeRepository = $app['orm.em']->getRepository(':Node');
	//create form
	$form = $app['form.factory']->createBuilder('form', array('name' =>''))
		->add('name', 'search', array(
			'constraints' => array(new Assert\NotBlank()),
			'attr' => array('class'=>'form-control', 'id'=>'form_search')
		))
		->add('send', 'submit', array(
			'attr' => array('class'=>'btn btn-default')
		))
		->getForm();
	$form->handleRequest($request);
	if ($form->isValid()) {
		$term =  $form->getData()['name'];
	}
	$itemsPerPage = $app['config']['pagination']['nodes_per_page'];

	//check form
	if ($term !== null) {
		$result = $nodeRepository->findBy(array('name'=>$term),null,$itemsPerPage, $itemsPerPage * ($page-1));
		$paginator = new Paginator($nodeRepository->countBy(array('name'=>$term)), $itemsPerPage, $page,$request->getUriForPath('/home/(:num)/'.$term));
	
		return $app['twig']->render('home.twig', array('form'=>$form->createView(),'nodes'=>$result, 'paginator' => $paginator));
	}
	$paginator = new Paginator($nodeRepository->count(), $itemsPerPage, $page,$request->getUriForPath('/home/(:num)'));

	//use the getAll function of the Node class to gather all the nodes
	$nodes = $nodeRepository->findBy(array(),null,$itemsPerPage, $itemsPerPage * ($page-1));
	return $app['twig']->render('home.twig', array('form'=>$form->createView(), 'nodes'=>$nodes, 'paginator'=>$paginator));
})
->value('page', 1)
->value('term', null)
->bind('home') ;

$app->match('/insert', function(Request $request) use($app) {

	//default data for the form to be displayed
	$node = new Node();

	//generate the form
	$form = $app['form.factory']->createBuilder(new NodeType($app), $node)->getForm();

	$form->handleRequest($request);

	if($form->isValid()) {
		$em = $app['orm.em'];
		foreach($node->getRelations() as $relation) {
			$relation->setStart($node); // Relation is on the owning side
			foreach($relation->getSecondaryRelations() as  $second) {
				$second->setStart($relation);
				$em->persist($second);
			}
			$em->persist($relation);
		}
		$em->persist($node);
		$em->flush();

		return $app->redirect($app->path('home'));
	}

	return $app['twig']->render('insert.twig', array('form'=>$form->createView()));
})->bind('insert');


$app->get('/node/{id}', function(Application $app, $id) {
	$node = $app['orm.em']->getRepository(':Node')->find($id);
	//get relations from and to this node
	$relFrom = $node->getRelations();
	$relTo = $app['orm.em']->getRepository(':Relation')->findBy(array("nodevalue"=>$id));
	$graphData = new GraphData();


	$graphData->addRelations($relFrom);
	$graphData->addRelations($relTo);

	return $app['twig']->render('node.twig', ['node'=>$node, 'relFrom'=>$relFrom, 'relTo'=>$relTo, 'graphNodes'=>$graphData->getNodes(), 'graphLinks'=>$graphData->getLinks()]);
})->bind('node');

$app->get('nodes/{value}', function(Application $app, $value) {
	//get all the nodes with $value as value for a property
	$nodes = $app['orm.em']->getRepository(':Node')->findByValue($value);
	return $app['twig']->render('nodes.twig', ['nodes'=>$nodes, 'value'=>$value]);
})->bind('nodes');


$app->match('/update/{id}', function(Application $app, Request $request, $id) {
	$em = $app['orm.em'];
	$noderepo = $em->getRepository(':Node');

	//get the node information for the given id
	$node = $noderepo->find($id);

	$form = $app['form.factory']->createBuilder(new NodeType($app), $node)->getForm();
	$form->handleRequest($request);

	//check form
	if ($form->isValid()) {
        foreach($node->getRelations() as $relation) {
			$relation->setStart($node);
			foreach($relation->getSecondaryRelations() as  $second) {
				$second->setStart($relation);
				$em->persist($second);
			}
			$em->persist($relation); // Relation is on the owning side
		}
        $em->persist($node);
        $em->flush();

		return $app->redirect($app->path('home'));
	}

	//display the form
	return $app['twig']->render('update.twig', array('form'=> $form->createView(), 'nodeid'=>$id));
})->bind('update');

$app->match('/search', function (Application $app, Request $request) {
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

		$result = $app["orm.em"]->getRepository(':Node')->findByDescription($term);
		//search in the database
		//$result = Node::findByDescription($term);

		return $app['twig']->render('search.twig', array('form'=>$form->createView(),'nodes'=>$result));
	}
	return $app['twig']->render('search.twig', array('form'=>$form->createView(), 'nodes'=>[]));
})->bind('search');

$app->get('/map', function(Application $app) {
	$geonodes = $app['orm.em']->getRepository(':Node')->getAllGeoNodes();

	return $app['twig']->render('map.twig', ['nodes'=>$geonodes]);
})->bind('map');

$app->get('/history/{id}', function(Application $app, $id) {
	//get node info
	$repo = $app['orm.em']->getRepository(':Node');
	$node = $repo->find($id);
	$history = $node->getHistory();

	// TODO after user implemented

	return $app['twig']->render('history.twig', ['node'=>$node, 'edits'=>$history]);
})->bind('history');

$app->match('/filter', function(Application $app, Request $request) {
	//create form with default data
	$default = array(
		'type'=>'',
		'property'=>'',
		'value' =>''
	);

	$form = $app['form.factory']->createBuilder(new FilterType($app), $default)->getForm();
	$form->handleRequest($request);

	if ($form->isValid()) {
		//get the property id and value
		$data = $form->getData();
		$id = $data['property'];
		$value = $data['value'];

		//get the nodes with this property and value
		$nodes = $app["orm.em"]->getRepository(':Node')->findByPropertyValue($id, $value);

		return $app['twig']->render('filter.twig', array('form'=>$form->createView(), 'nodes'=>$nodes));
	}
	return $app['twig']->render('filter.twig', array('form'=>$form->createView(), 'nodes'=>array()));
})->bind('filter');

$app->get('/graph', function(Application $app) {
	$relations = $app['orm.em']->getRepository(':Relation')->findAllNodeToNode();
	$graphData = new GraphData();

	foreach( $relations as $relation ){
		$graphData->addRelation($relation);
	}

	return $app['twig']->render('graph.twig', array('nodes'=>$graphData->getNodes(), 'links'=>$graphData->getLinks()));
})->bind('graph');

$app->match('/property', function(Application $app, Request $request) {
	$property = new Property();
	$form = $app['form.factory']->createBuilder(new PropertyType($app), $property)->getForm();
	$form->handleRequest($request);

	if($form->isValid()) {
		$em = $app['orm.em'];
		$em->persist($property);
		$em->flush();

		return $app->redirect($app->path('home'));
	}
	return $app['twig']->render('property.twig', array('form'=>$form->createView()));
})->bind('property');
