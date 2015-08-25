<?php

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="relation_log")
 */
class RelationLog {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;

	/**
	 * @ManyToOne(targetEntity="Relation")
	 * @JoinColumn(name="relation_id")
	 */
	private $relation;

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

}
