<?php
namespace MyApp\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\RelationRepository")
 * @Table(name="relations")
 * @EntityListeners({"MyApp\Entities\Listeners\RelationLogging"})
 */
class Relation extends ARelation {
    /**
     * @ManyToOne(targetEntity="Node", inversedBy="relations", cascade={"all"})
     * @JoinColumn(name="startnode")
     */
    private $startNode;

    /**
     * @OneToMany(targetEntity="SecondaryRelation", mappedBy="startRelation",cascade={"all"})
     **/
    private $secondaryRelations = [];

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

    function addSecondaryRelation(SecondaryRelation $relation) {
        $this->secondaryRelations[]  = $relation;
        $relation->setStart($this);
    }

    public function setSecondaryRelations($secondaryRelations) {
        $this->secondaryRelations = $secondaryRelations;
        foreach($secondaryRelations as $secondaryRelation) {
            $secondaryRelation->setStart($this);
        }
    }

    public function getSecondaryRelations() {
         return $this->secondaryRelations;
    }
}
