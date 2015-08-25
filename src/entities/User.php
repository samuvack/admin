<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/08/15
 * Time: 14:14
 */

namespace MyApp\Entities;


/**
 * A simple User model.
 *
 * @package SimpleUser
 */
class User extends \SimpleUser\User
{
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;
	/** @Column(type="text") */
	protected $email;
	/** @Column(type="text") */
	protected $password;
	/** @Column(type="text") */
	protected $salt;
	/** @Column(type="text") */
	protected $roles = array();
	/** @Column(type="text") */
	protected $name = '';
	/** @Column(type="integer") */
	protected $timeCreated;
	/** @Column(type="text") */
	protected $username;
	/** @Column(type="boolean") */
	protected $isEnabled = true;
	/** @Column(type="text") */
	protected $confirmationToken;
	/** @Column(type="integer") */
	protected $timePasswordResetRequested;
}

