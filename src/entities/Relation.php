<?php
namespace MyApp\Entities;
use \MyApp\Converters\StringConverter;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\RelationRepository")
 * @Table(name="relations")
 * @EntityListeners({"MyApp\Entities\Listeners\RelationLogging"})
 */
class Relation
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Property")
     * @JoinColumn(name="property", referencedColumnName="id")
     */
    private $property;
     /** @Column(type="text") */
    private $value;
     /** @Column(type="integer") */
    private $qualifier;
     /** @Column(type="text") */
    private $rank;

    /**
     * @ManyToOne(targetEntity="Node", inversedBy="relations")
     * @JoinColumn(name="startnode")
     */
    private $startNode;

    /**
     * @ManyToOne(targetEntity="Node")
     * @JoinColumn(name="nodevalue", referencedColumnName="id")
     */
    private $nodevalue;

    /**
     * @ManyToOne(targetEntity="Geometry")
     * @JoinColumn(name="geometryvalue")
     */
    private $geometryvalue;

    private $valueObject = null;

    function __construct($startNode = null, $property = null, $value = "", $nodevalue = null, $geometryvalue = null, $qualifier=null, $rank=null) {
        $this->startNode = $startNode;
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

    /*
     * @param Node the starting node
     */
    function setStart($new_start) {
        $this->startNode = $new_start;
    }

    /*
     * @return Node the starting node
     */
    function getStart() {
        return $this->startNode;
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
