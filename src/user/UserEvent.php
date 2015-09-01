<?php

namespace MyApp\User;

use Symfony\Component\EventDispatcher\Event;

class UserEvent extends Event
{
	protected $user;

	public function __construct(AUser $user)
	{
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser()
	{
		return $this->user;
	}
}
