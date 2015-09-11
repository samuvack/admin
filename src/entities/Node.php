<?php

namespace MyApp\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use MyApp\Values\RenderableValue;
use MyApp\FormTypes\NodeType;


/**
 * @Entity(repositoryClass="MyApp\Entities\Repositories\NodeRepository")
 * @Table(name="nodes")
 * @EntityListeners({"MyApp\Entities\Listeners\NodeLogging"})
 */
class Node implements RenderableValue {
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="text") */
    private $name;
    /** @Column(type="text", name="summary_nl") */
    private $description;
    /** @Column(type="tsvector", name="descr_nl") */
    private $descr;
    /**
     * @OneToMany(targetEntity="Relation", mappedBy="startNode", cascade={"all"})
     **/
    protected $relations = [];
    /**
     * @OneToMany(targetEntity="NodeLog", mappedBy="node", cascade={"all"})
     **/
    private $logs;
    /** @Column(type="float") */
    private $x = 0;
    /** @Column(type="float") */
    private $y = 0;
    /** @Column(type="text") */
    private $geo;
    /** @Column(type="geometry",options={"srid"=4326}) */
   private $geom;
    /**
     * @ManyToOne(targetEntity="Layer", inversedBy="nodes", cascade={"all"})
     * @JoinColumn(name="layer_id")
     */
    protected $layer;

    function __construct($name="", $description="", $descr=null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->descr = $descr;
    }

    function getId()
    {
        return $this->id;
    }

    function setName($new_name)
    {
        $this->name = (string) $new_name;
    }

    function getName()
    {
        return $this->name;
    }

    function setDescription($new_description)
    {
        $this->description = (string) $new_description;
    }

    function getDescription()
    {
        return $this->description;
    }

    function setDescr($new_descr)
    {
        $this->descr = (string) $new_descr;
    }

    function getDescr()
    {
        return $this->descr;
    }

    /**
    * Adds the relation to the attribute relations
    *
    * @param Relation newRelation the relation to be added
    */
    public function addRelation(Relation $newRelation)
    {
        array_push($this->relations, $newRelation);
        $newRelation->setStart($this);
    }

    /*
     * @return Relation[] relations of this node
     */
    public function getRelations() {
        return $this->relations;
    }

    public function getHistory() {
        return $this->logs;
    }
    /**
    * Removes the given relation from the relations attribute
    *
    * @param Relation $oldRelation
    */
    function removeRelation(Relation $oldRelation)
    {
        if(($key = array_search($oldRelation, $this->relations)) !== FALSE) {
            unset($this->relations[$key]);
            $oldRelation->setStart(null);
        }
    }

    /**
     * @return String simple string for use in e.g. the graph
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * Get FormType
     */
    public function getFormType(\Silex\Application $app) {
        return new NodeType($app);
    }

    /**
     * Extended view, for detailed representation
     */
    public function render(\Twig_Environment $env, array $params) {
        $params = array_merge(array('node'=> $this, 'link'=>false), $params);
        $env->display("@values/node.twig", $params);
    }

    public function filter(Relation $relation) {
        // TODO: Implement filter() method.
    }

    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

    public function getLayer(){
        return $this->layer;
    }

    public function setLayer($layer) {
        $this->layer = $layer;
    }

    public function setX($x) {
        $this->x = $x;
        $this->geo = $this->geom = "POINT( ".$this->x." ".$this->y.")";
    }

    public function setY($y) {
        $this->y = $y;
        $this->geo = $this->geom = "POINT( ".$this->x." ".$this->y.")";
    }
}
