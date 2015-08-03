<?php

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="nodes")
 */
class Node {
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /** @Column(type="text") */
    private $name;
    /** @Column(type="text") */
    private $description;
    /** @Column(type="text") */
    private $descr;
    private $relations;

    function __construct($id = null, $name="", $description="", $descr=null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->id = $id;
        $this->descr = $descr;
        $this->relations = array();
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
}
