<?php

namespace MyApp\Entities;
use \MyApp\Converters\StringConverter;
/**
 * @Entity
 * @Table(name="relations_log")
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

	/**
	 * @ManyToOne(targetEntity="Node")
	 * @JoinColumn(name="startid")
	 */
	private $startnode;

	/** @Column(type="text") */
	private $value;
	/** @Column(type="text") */
	private $nodevalue;
	/** @Column(type="text") */
	private $geometryvalue;
	/** @Column(type="integer") */
	private $qualifier;

	/** @Column(type="text") */
	private $rank;
	/** @Column(type="log_action") */
	private $action;
	/** @Column(type="datetime") */
	private $action_time;

	/**
	 * @GeneratedValue
	 * @Column(type="integer",name="action_by")
	 */
	private $user;

	public function __construct(Relation $relation, User $user, $action) {
		$this->user = $user->getId();
		$this->relation = $relation;
		$this->startnode = $relation->getStart();
		$this->setValue($relation->getValue());
		$this->rank = $relation->getRank();
		$this->action = $action;
		$this->qualifier = $relation->getQualifier();
	}

	private function setValue($new_value) {
		$this->nodevalue = $this->geometryvalue = $this->value = null;
		$this->valueObject = $new_value;

		if($new_value instanceof Node)
			$this->nodevalue = $new_value;
		elseif ($new_value instanceof Geometry )
			$this->geometryvalue = $new_value;
		else {
			$this->value = $new_value;
		}
	}

}
