<?php
use \MyApp\FormTypes\FormTypeProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
$app->get('/form/{property}', function(Application $app, Request $request, $property) {
	$result = $app["orm.em"]->getRepository(':Property')->find($property);
	$type = $result->getDataType();
	$formType = $app['mapping.manager']->getFormType($type);

	$form = $app['form.factory']->createBuilder($formType)->getForm();
	$form->handleRequest($request);
	return $app['twig']->render('values/ajaxform.twig', array('form'=> $form->createView()));
})->bind('form');
