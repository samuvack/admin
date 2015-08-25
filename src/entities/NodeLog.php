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
	/** @Column(type="timestamp") */
	private $actionTime;

	/**
	 * @ManyToOne(targetEntity="User")
	 * @JoinColumn(name="user_id")
	 */
	private $user;

	public function __construct($node, $user, $action) {
		$this->user = $user;
		$this->node = $node;
		$this->name = $node->getName();
		$this->description = $node->getDescription();
		$this->action = $action;
	}

}
