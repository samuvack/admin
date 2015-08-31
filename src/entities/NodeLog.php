<?php

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="nodes_log")
 */
class NodeLog {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;

	/**
	 * @ManyToOne(targetEntity="Node")
	 * @JoinColumn(name="node_id")
	 */
	private $node;

	/** @Column(type="text") */
	private $name;

	/** @Column(type="text") */
	private $description;

	/** @Column(type="tsvector") */
	private $descr;

	/** @Column(type="log_action") */
	private $action;
	/**
	* @Column(type="datetime", nullable=false)
 	* @GeneratedValue
	*/
	private $action_time;

	/**
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(name="action_by", referencedColumnName="id")
	 **/
	private $user;

	public function __construct(Node $node, User $user, $action) {
		$this->user = $user;
		$this->node = $node;
		$this->name = $node->getName();
		$this->description = $node->getDescription();
		$this->descr = $node->getDescr();
		$this->action = $action;
		$this->action_time = new \DateTime();
	}

	public function getAction() {
		return $this->action;
	}

	public function getTime() {
		return $this->action_time;
	}

	public function getUser() {
		return $this->user;
	}

}
