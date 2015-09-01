<?php
$app->method('GET')->match('/user/{id}', function(Application $app, $id) {
	$user = $app['user.manager']->getUser($id);

	if (!$user) {
		throw new NotFoundHttpException('No user was found with that ID.');
	}

	if (!$user->isEnabled() && !$app['security']->isGranted('ROLE_ADMIN')) {
		throw new NotFoundHttpException('That user is disabled (pending email confirmation).');
	}

	return $app['twig']->render($this->getTemplate('view'), array(
		'layout_template' => $this->getTemplate('layout'),
		'user' => $user,
		'imageUrl' => $this->getGravatarUrl($user->getEmail()),
	));
});
