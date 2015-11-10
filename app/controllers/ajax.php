<?php
use MyApp\Entities\Relation;
use \MyApp\FormTypes\FormTypeProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app->get('/ajax/form/{property}', function(Application $app, Request $request, $property) {
	$result = $app["orm.em"]->getRepository(':Property')->find($property);
	$type = $result->getDataType();
	$formType = $app['mapping.manager']->getFormType($type);

	$form = $app['form.factory']->createBuilder($formType)->getForm();
	$form->handleRequest($request);
	return $app['twig']->render('ajax/property.twig', array('form'=> $form->createView()));
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

$app->get('/ajax/addGraphLink/{parent}/{child}', function(Application $app, $parent, $child) {
	$nodeRepo = $app['orm.em']->getRepository(':Node');
	// Generate Doctrine proxies for $parent and $child
	$parent = $nodeRepo->find($parent);
	$child = $nodeRepo->find($child);
	if($child === null || $parent === null) {
		return new \Symfony\Component\HttpFoundation\Response("Missing node", 404);
	}
	$propRepo = $app['orm.em']->getRepository(':Property');
	$is_part_of = $propRepo->findOneBy(array('name'=>'is part of', 'datatype'=>'node'));
	if($is_part_of === null) {
		return new \Symfony\Component\HttpFoundation\Response("No property found", 404);
	}
	$relation = new Relation($parent, $is_part_of, $value = "", $child);
	$app['orm.em']->persist( $relation);
	$app['orm.em']->flush();

	return $app['twig']->render('ajax/relation.twig',array('relation'=>$relation));
})->bind('form');
