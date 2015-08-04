<?php
namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="statements")
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
     * @JoinColumn(name="propertyname", referencedColumnName="id")
     */
    private $property;
     /** @Column(type="dynamicType") */
    private $value;
     /** @Column(type="integer") */
    private $qualifier;
     /** @Column(type="text") */
    private $rank;

    /**
     * @ManyToOne(targetEntity="Node", inversedBy="relations")
     * @JoinColumn(name="startid")
     */
    private $startNode;

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
        $this->start_node = $new_start;
    }

    /*
     * @return Node the starting node
     */
    function getStart() {
        return $this->start_node;
    }

    function setProperty($new_prop) {
        $this->property = $new_prop;
    }

    function getProperty() {
        return $this->property;
    }

    function setValue($new_value) {
        $this->value = (string) $new_value;
    }

    function getValue() {
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
