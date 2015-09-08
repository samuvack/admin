<?php
namespace MyApp\Entities;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\RelationRepository")
 * @Table(name="relations")
 * @EntityListeners({"MyApp\Entities\Listeners\RelationLogging"})
 */
class Relation extends ARelation {
    /**
     * @ManyToOne(targetEntity="Node", inversedBy="relations")
     * @JoinColumn(name="startnode")
     */
    private $startNode;

    /**
     * @OneToMany(targetEntity="SecondaryRelation", mappedBy="startRelation")
     **/
    private $secondary_relations = [];

    function __construct($startNode = null, $property = null, $value = "", $nodevalue = null, $geometryvalue = null, $qualifier=null, $rank=null) {
        parent::__construct($property,$value, $nodevalue, $geometryvalue, $qualifier, $rank);
        $this->startNode = $startNode;
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

    function addRelation(SecondaryRelation $relation) {
        $this->secondary_relations[]  = $relation;
    }

    public function getRelations() {
        return $this->secondary_relations;
    }
}
