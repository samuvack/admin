<?php

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="secondary_relations")
 */
class SecondaryRelation extends ARelation {
	/**
	 * @ManyToOne(targetEntity="Relation", inversedBy="secondary_relations")
	 * @JoinColumn(name="parent_relation")
	 */
	private $startRelation;

	public function __construct($startRelation = null, $property = null,
								$value = "", $nodevalue = null, $geometryvalue = null, $qualifier=null, $rank=null) {
		parent::__construct($property,$value, $nodevalue, $geometryvalue, $qualifier, $rank);
		$this->startRelation = $startRelation;
	}

	public function getStart() {
		return $this->startRelation;
	}

	public function setStart(Relation $new_start) {
		$this->startRelation = $new_start;
	}
}
