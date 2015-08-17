<?php
require_once __DIR__.'/../../vendor/autoload.php';
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use MyApp\Entities\Node;
use MyApp\Entities\Property;
use MyApp\Entities\Relation;
use MyApp\Types\FilterType;
require_once __DIR__.'/../ChromePhp.php';

$app->match('/', function(Application $app, Request $request) {
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

	//check form
	if ($form->isValid()) {
		//get the search term
		$data = $form->getData();
		$term = $data['name'];
		$result = $nodeRepository->findBy(array('name'=>$term));

		return $app['twig']->render('home.html', array('form'=>$form->createView(),'nodes'=>$result));
	}

	//use the getAll function of the Node class to gather all the nodes
	$nodes = $nodeRepository->findAll();
	return $app['twig']->render('home.html', array('form'=>$form->createView(), 'nodes'=>$nodes));
})->bind('home');

$app->match('/insert', function(Request $request) use($app) {

	//default data for the form to be displayed
	$node = new Node();

	//generate the form
	$form = $app['form.factory']->createBuilder(new NodeType($app), $node)->getForm();

	$form->handleRequest($request);

	if($form->isValid()) {
		$em = $app['orm.em'];
		foreach($node->getRelations() as $relation)
			$em->persist($relation); // Relation is on the owning side
		$em->persist($node);
		$em->flush();

		return $app->redirect($app->path('home'));
	}

	return $app['twig']->render('insert.html', array('form'=>$form->createView()));
})->bind('insert');

$app->match('/import', function(Request $request) use($app) {

	//generate the form
	$form = $app['form.factory']->createBuilder('form')
		->add('file', 'file')
		->add('Import', 'submit', array('label'=>'Start import'))
		->getForm();

	//handle the form
	$form->handleRequest($request);
	if($form->isValid()){
		//get the file
		$file=$form['file']->getData();
		//check the extension of the selected file
		if($file->guessExtension() == 'txt') {
			//get the file contents
			$contents = file($file->getPathname());

			//get column names
			//remove line break
			//split string by ; in array
			$contents[0]=preg_replace( "/\r|\n/", "", $contents[0] );
			$columns = explode(";", $contents[0]);

			//show form to select the node's name and description columns
			//...TO BE WRITTEN
			//... It is possible to have multiple node columns in one file
			//... use and array: array([node1, descr1], [node2,descr2])??
			//store the index of columns
			$nameCol = 2;
			$descriptionCol = null;

			//show form to select the relation columns
			//...TO BE WRITTEN
			//... repeat for all identified node name columns
			//store in array as index of $columns
			$relColumns = array(4,5,6,0);

			//show form to select the appropriate property for each selected column
			//...TO BE WRITTEN
			//store in array using the id of the property
			$relProps = array(1,6,5,5);

			//ask the type of the node, for the obligatory node type
			//..TO BE WRITTEN
			//... Repeat for each node column
			$nodeType = 'spoor';

			//loop the rows
			for ($i=1; $i<count($contents); $i++){
				//convert the string into array by separator ;
				$contents[$i]=preg_replace( "/\r|\n/", "", $contents[$i]);
				$contents[$i]=explode(";", $contents[$i]);

				//create new node
				//...TO BE WRITTEN
				//... change for multiple nodes
				$nodeName = $contents[$i][$nameCol];
				//check if a description column exists
				if($descriptionCol) {
					$nodeDescription = $contents[$i][$descriptionCol];
				} else {
					//provide a default description in which to replace the name
					//..TO BE WRITTEN
					$nodeDescription = $nodeType ." met naam " .$nodeName;
				}
				//before adding, check if node with this name-description combi does not already exist
				//...TO BE WRITTEN
				$node = new Node(null, $nodeName, $nodeDescription, null);

				//create new relations and add to the node, no qualifier and rank
				for($j=0; $j<count($relColumns); $j++){
					//check if a value exist, otherwise do not store the relation
					$relValue = $contents[$i][$relColumns[$j]];

					if($relValue) {
						//check the datatype of the property
						$relType = Property::findById($relProps[$j])->getDatatype();

						//change value based on property datatype
						if($relType == 'node'){ //if the datatype is node
							//search if a node with this name exists
							$nodeValue = Node::findByName($relValue);
							if($nodeValue){
								//if exists, store id as value of the relation
								//...TO BE WRITTEN
								//...Allow user to confirm that this is the right value based on the node description
								//...Allow user to select the right value if multiple nodes with this name exist
								$relValue = $nodeValue->getId();
								$relation = new Relation(null, null, $relProps[$j], $relValue, null, null);
								$node->addRelation($relation);
							} else {
								//if not exists, show a dialog with similar nodes or possiblity to add new
								//TO BE WRITTEN
							}
						} elseif($relType == 'data'){ //if the datatype is date
							//change the representation of the node ~ISO8601 or ISO19108
						} elseif($relType == 'geometry') { //if datatype is geometry
							//convert to PostGIS geometry
						} else {
							$relation = new Relation(null, null, $relProps[$j], $relValue, null, null);
							$node->addRelation($relation);
						}


					}

				}

				//add an obligatory 'is of type attribute for all values
				$relation = new Relation(null, null, 1, $nodeType, null, null);
				$node->addRelation($relation);

				//save the node to the db
				//...To BE WRITTEN
				//...Save all the nodes to the database
				$node->save();
			}

		}

		return $app->render('import.html', array('form'=>$form->createView(), 'text'=>'file successfully imported'));
	}

	return $app['twig']->render('import.html', array('form'=>$form->createView(), 'text'=>'no file imported'));
})->bind('import');

$app->get('/node/{id}', function(Application $app, $id) {
	$node = $app['orm.em']->getRepository(':Node')->find($id);
	//get relations from and to this node
	$relFrom = $node->getRelations();
	$relTo = $app['orm.em']->getRepository(':Relation')->findBy(array("nodevalue"=>$id));
	//$relTo = $node->findEndRelations();

	return $app['twig']->render('node.html', ['node'=>$node, 'relFrom'=>$relFrom, 'relTo'=>$relTo]);
})->bind('node');

$app->get('nodes/{value}', function(Application $app, $value) {
	//get all the nodes with $value as value for a property
	$nodes = $app['orm.em']->getRepository(':Node')->findByValue($value);
	return $app['twig']->render('nodes.html', ['nodes'=>$nodes, 'value'=>$value]);
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
			$em->persist($relation); // Relation is on the owning side
		}
        $em->persist($node);
        $em->flush();

		return $app->redirect($app->path('home'));
	}

	//display the form
	return $app['twig']->render('update.html', array('form'=> $form->createView(), 'nodeid'=>$id));
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

		return $app['twig']->render('search.html', array('form'=>$form->createView(),'nodes'=>$result));
	}
	return $app['twig']->render('search.html', array('form'=>$form->createView(), 'nodes'=>[]));
})->bind('search');

$app->get('/map', function(Application $app) {
	$geonodes = $app['orm.em']->getRepository(':Node')->getAllGeoNodes();

	return $app['twig']->render('map.html', ['nodes'=>$geonodes]);
})->bind('map');

$app->get('/history/{id}', function(Application $app, $id) use ($DB) {
	//get node info
	$node = Node::findById($id);
	$history = $node->findHistory();

	// TODO after user implemented

	return $app['twig']->render('history.html', ['node'=>$node, 'edits'=>$history]);
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

		return $app['twig']->render('filter.html', array('form'=>$form->createView(), 'nodes'=>$nodes));
	}
	return $app['twig']->render('filter.html', array('form'=>$form->createView(), 'nodes'=>array()));
})->bind('filter');

$app->get('/graph', function(Application $app, Request $request) {
	$relations = $app['orm.em']->getRepository(':Relation')->findAllNodeToNode();

	$nodes = [];
	$idConverter = [];
	$links = [];
	/*
	 * TODO: Functions like these are kindly ugly in PHP, find alternative
	 * This isn't (node.)js
	 *
	 * Also, call $idConverter and $nodes by reference (&)
	 */
	$addNode = function($node) use( &$idConverter, &$nodes) {
		if( ! isset($idConverter[$node->getId()])) {
			$idConverter[$node->getId()] = sizeof($nodes);
			$nodes[] = [
				'name' => $node->getName(),
				'id' => $node->getId()
			];
		}
	};

	foreach( $relations as $relation ){
		$addNode($relation->getStart());
		$addNode($relation->getValue());
		$links[] = [
			'source' => $relation->getStart()->getId(),
			'target' =>$relation->getValue()->getId()
		];
	}

	/*$nodeObjects= $app['orm.em']->getRepository(':Node')->findAll();
	$nodes = [];
	$idConverter = [];

	//prepare array for passing
	foreach( $nodeObjects as $node ) {
		$idConverter[$node->getId()] = sizeof($nodes);
		$nodes[]= [
			'name' => $node->getName(),
			'id' => $node->getId()
		];

	}*/

	/*
	 * trust on Doctrine proxies to not load every node again
	 * http://stackoverflow.com/a/17787070/4701236
	 */
	/*$linkObjects = $app['orm.em']->getRepository(':Relation')->findBy();
	$links = [];

	foreach( $linkObjects as $link) {
		$links[] = array(
			'source'=>$idConverter[$link->id1],
			'target'=>$idConverter[$link->id2]
		);
	}*/

	return $app['twig']->render('graph.html', array('nodes'=>$nodes, 'links'=>$links));
})->bind('graph');
