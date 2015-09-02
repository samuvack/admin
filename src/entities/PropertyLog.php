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
	private $property;
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
	/**
	 * @GeneratedValue
	 * @Column(type="datetime")
	 */
	private $action_time;
	/**
	 * @Column(type="integer",name="action_by")
	 */
	private $user;

	public function __construct(Property $property, User $user, $action) {
		$this->user = $user->getId();
		$this->property = $property;
		$this->name = $property->getName();
		$this->datatype = $property->getDatatype();
		$this->description = $property->getDescription();
		$this->descr = $property->getDescr();
		$this->action = $action;
		$this->action_time = new \DateTime();
	}

	public function getEntity(){
		return $this->property;
	}

	public function getName() {
		return $this->name;
	}
}
