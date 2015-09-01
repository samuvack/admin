<?php
namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="properties")
 * @EntityListeners({"MyApp\Entities\Listeners\PropertyLogging"})
 */
class Property
{
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
    private $datatype;
     /** @Column(type="tsvector") */
    private $descr;

    function __construct($name = "", $description ="", $datatype="", $descr=null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->datatype = $datatype;
        $this->descr = $descr;
    }

    function setId($new_Id)
    {
            $this->id = (int) $new_Id;
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

    function setDatatype($new_datatype)
    {
            $this->datatype = $new_datatype;
    }

    function getDatatype()
    {
            return $this->datatype;
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
