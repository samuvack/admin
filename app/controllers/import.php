<?php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use MyApp\Files\Import\SpreadsheetParser;
use MyApp\Files\Import\TraceManager;
use MyApp\Files\Import\HeaderManager;
use MyApp\FormTypes\Import\NodeType as NodeImportType;
$import = $app['controllers_factory'];

$import->match('/', function(Silex\Application $app, Request $request) {

	//generate the form
	$form = $app['form.factory']->createBuilder('form')
		->add('file', 'file')
		/*->add('header_lines', 'integer', array('data'=>1))
		->add('trace', new NodeImportType($app))
		->add('context', new NodeImportType($app))
		->add('structure', new NodeImportType($app))*/
		->add('import', 'submit', array('label'=>'Start import'))
		->getForm();


	$form->handleRequest($request);
	if($form->isValid()) {
		$dir = $app['config']['importFileDir'];
		$file = $form['file']->getData();
		$i = 0;
		$location = tempnam($dir, 'import');
		move_uploaded_file($file, $location);
		$manager = new HeaderManager();
		$parser = new SpreadsheetParser($location, $manager);
		if($columns = $parser->parse())
		return $app->render('import/columns.twig', array(
			'file'=>$location,
			'columns'=>$columns,
			'properties' => $app['orm.em']->getRepository(':Property')->findAll()
		));
	}

	return $app['twig']->render('import/file.twig', array('form'=>$form->createView(), 'text'=>'no file imported'));
})->bind('import');
$import->match('/start', function(Silex\Application $app, Request $request) {
	$columns = json_decode($_POST['columns'],true);

	$file = $_POST['file'];

	$manager = new TraceManager($app['orm.em'],$columns);
	$parser = new SpreadsheetParser($file, $manager);
	$parser->parse();
	return $app->redirect($app->path('home'));


//	return $app['twig']->render('import/file.twig', array('form'=>$form->createView(), 'text'=>'no file imported'));
})->bind('import/start');

return $import;
