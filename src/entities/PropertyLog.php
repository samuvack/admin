<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 26/08/15
 * Time: 10:50
 */

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="properties_log")
 */
class PropertyLog {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;

	/**
	 * @ManyToOne(targetEntity="Property")
	 * @JoinColumn(name="property_id")
	 */
	private $node;
	/** @Column(type="text") */
	private $name;
	/** @Column(type="text") */
	private $description;
	/** @Column(type="text") */
	private $datatype;
	/** @Column(type="text") */
	private $descr;
	/** @Column(type="log_action") */
	private $action;
	/** @Column(type="datetime") */
	private $action_time;
	/**
	 * @Column(type="integer",name="action_by")
	 */
	private $user;
}
