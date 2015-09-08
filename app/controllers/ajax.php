<?php
use \MyApp\FormTypes\FormTypeProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app->get('/ajax/form/{property}', function(Application $app, Request $request, $property) {
	$result = $app["orm.em"]->getRepository(':Property')->find($property);
	$type = $result->getDataType();
	$formType = $app['mapping.manager']->getFormType($type);

	$form = $app['form.factory']->createBuilder($formType)->getForm();
	$form->handleRequest($request);
	return $app['twig']->render('values/ajaxform.twig', array('form'=> $form->createView()));
})->bind('form');


$app->get('/ajax/nodeInfo/{id}', function(Application $app, Request $request, $id) {
	$result = $app["orm.em"]->getRepository(':Node')->find($id);

	return $app['twig']->render('values/node.twig', array('node'=> $result, 'link'=>false));
});

$app->get('/ajax/nodeInfoByGeo/{id}', function(Application $app, Request $request, $id) {
	//TODO:first search all geoproperties, then loop all these ids with the geometry id $id
	$result = $app["orm.em"]->getRepository(':Node')->findByPropertyValue(3, $id);
	return $app['twig']->render('values/node.twig', array('node'=> $result[0], 'link'=>false));
});