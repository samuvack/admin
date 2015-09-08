<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 08/09/15
 * Time: 09:35
 */

namespace MyApp\Entities;
use \MyApp\Converters\StringConverter;
use Symfony\Component\Validator\Constraints as Assert;


class ARelation {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	protected $id;

	/**
	 * @ManyToOne(targetEntity="Property")
	 * @JoinColumn(name="property", referencedColumnName="id")
	 */
	protected $property;
	/** @Column(type="text") */
	protected $value;
	/** @Column(type="integer") */
	protected $qualifier;
	/** @Column(type="text") */
	protected $rank;

	/**
	 * @ManyToOne(targetEntity="Node", cascade={"persist"})
	 * @JoinColumn(name="nodevalue", referencedColumnName="id")
	 */
	protected $nodevalue;

	/**
	 * @ManyToOne(targetEntity="Geometry")
	 * @JoinColumn(name="geometryvalue")
	 */
	protected $geometryvalue;

	protected $valueObject = null;

	function __construct($startNode = null, $property = null, $value = "", $nodevalue = null, $geometryvalue = null, $qualifier=null, $rank=null) {
		$this->property = $property;
		$this->nodevalue = $nodevalue;
		$this->geometryvalue = $geometryvalue;
		$this->value = $value;
		$this->qualifier = $qualifier;
		$this->rank = $rank;
	}

	function getId() {
		return $this->id;
	}


	function setProperty($new_prop) {
		$this->property = $new_prop;
	}

	function getProperty() {
		return $this->property;
	}

	function setValue($new_value) {
		$this->nodevalue = $this->geometryvalue = $this->value = null;
		$this->valueObject = $new_value;

		if($new_value instanceof Node)
			$this->nodevalue = $new_value;
		elseif ($new_value instanceof Geometry )
			$this->geometryvalue = $new_value;
		else {
			$converter = StringConverter::getConverter($this->property->getDataType());
			$this->value = $converter->toString($new_value);
		}
	}

	function getValue() {
		/*
		 * Smelly code is smelly, but embeddings do not support cross-table fields yet
		 */
		if ($this->valueObject !== null)
			return $this->valueObject;

		$type = $this->property->getDataType();

		if ($type == 'node')
			$this->valueObject = $this->nodevalue;
		elseif($type == 'geometry')
			$this->valueObject = $this->geometryvalue;
		else {
			$converter = StringConverter::getConverter($this->property->getDataType());
			$this->valueObject = $converter->toObject($this->value);
		}

		return $this->valueObject;
	}

	public function _getValue() {
		return $this->value;
	}

	function setQualifier($new_qualifier) {
		$this->qualifier = (string) $new_qualifier;
	}

	function getQualifier() {
		return $this->qualifier;
	}

	function setRank($new_rank) {
		$this->rank = (string) $new_rank;
	}

	function getRank() {
		return $this->rank;
	}
}
