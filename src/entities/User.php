<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/08/15
 * Time: 14:14
 */

namespace MyApp\Entities;
use MyApp\Values\RenderableValue;


/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\MyRepository")
 * @Table(name="users")
 */
class User extends \MyApp\User\AUser implements RenderableValue
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
	/** @Column(type="simple_array") */
	protected $roles = array('ROLE_USER');
	/** @Column(type="text") */
	protected $name = '';
	/** @Column(type="integer", name="time_created") */
	protected $timeCreated;
	/** @Column(type="text") */
	protected $username;
	/** @Column(type="boolean", name="is_enabled") */
	protected $isEnabled = true;
	/** @Column(type="text", name="confirmation_token") */
	protected $confirmationToken;
	/** @Column(type="integer", name="time_password_reset_requested") */
	protected $timePasswordResetRequested;

	/**
	 * @OneToMany(targetEntity="NodeLog", mappedBy="user")
	 **/
	private $nodeLogs;
	/**
	 * @OneToMany(targetEntity="RelationLog", mappedBy="user")
	 **/
	private $relationLogs;

	public function getLogs() {
		return new LogIterator($this->nodeLogs, $this->relationLogs);
	}

	/**
	 * @return String simple string for use in e.g. the graph
	 */
	public function __toString() {
		return $this->getRealUsername();
	}

	/**
	 * Get FormType
	 */
	public function getFormType(\Silex\Application $app) {
		// TODO: Implement getFormType() method.
	}

	/**
	 * Extended view, for detailed representation
	 */
	public function render(\Twig_Environment $env, array $params) {
		$params = array_merge(array('user'=> $this, 'link'=>false), $params);
		$env->display('values/user.twig',$params);
	}
}

class LogIterator implements \Iterator {
	private $nodeLog;
	private $relationLog;
	private $nodeIndex = 0;
	private $relationIndex = 0;
	public function __construct($nodeLog, $relationLog) {
		$this->nodeLog = $nodeLog;
		$this->relationLog =$relationLog;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	public function current() {
		if(! $this->valid())
			return null;

		if($this->nodeIndex >= sizeof($this->nodeLog)) {
			return $this->relationLog[$this->relationIndex];
		} else if ($this->relationIndex >= sizeof($this->relationLog)) {
			return $this->nodeLog[$this->nodeIndex];
		} else {
			$node = $this->nodeLog[$this->nodeIndex];
			$relation = $this->relationLog[$this->relationIndex];
			return ($node.getTime() <= $relation.getTime())? $node : $relation;
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	public function next() {
		$node = $this->nodeLog[$this->nodeIndex];
		$relation = $this->relationLog[$this->relationIndex];
		if($this->nodeIndex >= sizeof($this->nodeLog)) {
			++$this->relationIndex;
		} else if ($this->relationIndex >= sizeof($this->relationLog)) {
			++$this->nodeIndex;
		} else if($node->getTime() < $relation->getTime()) {
			++$this->nodeIndex;
		} else {
			++$this->relationIndex;
		}
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->nodeIndex + $this->relationIndex;
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 */
	public function valid() {
		return ($this->nodeIndex < sizeof($this->nodeLog) || $this->relationIndex < sizeof($this->relationLog));
	}

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	public function rewind() {
		$this->nodeIndex = $this->relationIndex = 0;
	}
}
