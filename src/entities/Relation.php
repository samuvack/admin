<?php
namespace MyApp\Entities;
use \MyApp\Converters\StringConverter;

/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\RelationRepository")
 * @Table(name="relations")
 * @HasLifecycleCallbacks
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

    function __construct($start_node = null, $property = null, $value = "", $qualifier=null, $rank=null) {
        $this->start_node = $start_node;
        $this->property = $property;
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

    /**
    * Uses getAll() to get all statements with a geometry property
    * The value of statement is text representation
    *
    * @return Relation[]
    */
    static function getGeometryRelations()
    {
            $found_relations = array();
            $relations = Relation::getAll();

            foreach($relations as $rel) {
                    $rel_prop = $rel->getProperty();
                    $prop_datatype = $rel_prop->getDatatype();
                    $rel_value = $rel->getValue();
                    if($prop_datatype == 'geometry') {
                            $geom = $GLOBALS['DB']->query("SELECT st_astext(geom) as geom FROM geometries WHERE id=" .$rel_value .";");
                            $result = $geom->fetch(PDO::FETCH_ASSOC);
                            $geom_text = $result['geom'];
                            $rel->setValue($geom_text);
                            array_push($found_relations, $rel);
                    }
            }
            return $found_relations;
    }
}
