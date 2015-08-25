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
	/** @Column(type="datetime") */
	private $action_time;

	/**
	 * @Column(type="integer",name="action_by")
	 */
	private $user;

	public function __construct(Node $node, User $user, $action) {
		$this->user = $user->getId();
		$this->node = $node;
		$this->name = $node->getName();
		$this->description = $node->getDescription();
		$this->descr = $node->getDescr();
		$this->action = $action;
		$this->actionTime = new \DateTime();
	}

}
