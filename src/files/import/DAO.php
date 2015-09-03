<?php

/*
 * Class for providing and caching entities while parsing a file to import data
 */
namespace MyApp\Files\Import;
use \MyApp\Entities\Node;
use \MyApp\Entities\Relation;
use \MyApp\Entities\Property;
class DAO {
	private $nodes = array();
	private $nodeRelations = array();
	private $relations = array();
	private $geoRelations = array();
	private $nodeRepo;
	private $propRepo;
	private $relRepo;
	private $properties = array();
	private $propertiesById = array();
	private $em;
	public function __construct(\Doctrine\ORM\EntityManager $em) {
		$this->nodeRepo = $em->getRepository(':Node');
		$this->propRepo = $em->getRepository(':Property');
		$this->relRepo =  $em->getRepository(':Relation');
		$this->em = $em;
	}

	public function getNode($name, $description = '') {
		if(isset($this->nodes[$name])) {
			return $this->nodes[$name];
		}

		$node = $this->nodeRepo->findOneBy(array('name' => $name));
		if($node === null) {
			$node = new Node($name, $description);
			$this->em->persist($node);
		}

		$this->nodes[$name] = $node;
		return $node;
	}

	/*
	 * @param name of the wanted property
	 */
	public function getProperty($name) {
		if(isset($this->properties[$name])) {
			return $this->properties[$name];
		}

		$prop = $this->propRepo->findOneBy(array('name' => $name));
		if($prop === null) {
			throw new \Exception("Property not found");
		}

		$this->_addProperty($prop);
		return $prop;
	}

	public function getPersistedProperty(Property $prop) {
		return $this->getPropertyById($prop->getId());
	}

	/*
	 * Add a property object to the maps
	 */
	private function _addProperty(Property $prop) {
		$this->properties[$prop->getName()] = $prop;
		$this->propertiesById[$prop->getId()] = $prop;
	}

	public function getPropertyById($id) {
		if(isset($this->propertiesById[$id])) {
			return $this->propertiesById[$id];
		}

		$prop = $this->propRepo->find( $id );
		if($prop === null) {
			throw new \Exception("Property not found");
		}
		$this->_addProperty($prop);
		return $prop;
	}

	public function createNodeRelation(Node $parentNode, Node $childNode, Property $property = null) {
		if($property === null) {
			$property = $this->getPropertyById(5); // 'is_part_of' property
		}
		$relation = new Relation($parentNode, $property, null, $childNode);
		$this->addRelation($relation);
	}

	/*
	 * Limit the nodes in the entitymanager to avoid OOM-errors
	 */
	public function limitCache() {
		if(sizeof($this->nodes) >= 200) {
			$this->em->flush();
			$this->em->clear(':Node');
			$this->em->clear(':Relation');
			$this->nodes = array();
		}
	}

	/*
	 * Add a relation
	 * If the relation has datatype node, check if this relation isn't a duplicate
	 */
	public function addRelation(Relation $relation) {
		if($relation->getProperty()->getDataType() == 'node') {
			if(! $this->validateNodeRelation($relation))
				return;
		} else if($relation->getProperty()->getDataType() == 'geometry') {
			if(!$this->validateGeometryRelation($relation)) {
				return;
			}
		} else {
			if(!$this->validateRelation($relation)) {
				return;
			}
		}
		$this->em->persist($relation);
	}

	private function validateNodeRelation(Relation $relation) {
		if(! isset($this->nodeRelations[$relation->getStart()->getId()])) {
			$this->nodeRelations[$relation->getStart()->getId()] = array();
		} else if(isset($this->nodeRelations[$relation->getStart()->getId()][$relation->getValue()->getId()])) {
			return false; //relation already exists
		}
		$this->nodeRelations[$relation->getStart()->getId()][$relation->getValue()->getId()] = true;
		$dbrel = $this->em->getRepository(':Relation')->findOneBy(array('startNode' => $relation->getStart(), 'nodevalue' =>  $relation->getValue()));
		if($dbrel !== null) {
			return false;
		}
		return true;
	}

	private function validateGeometryRelation(Relation $relation) {
		if(! isset($this->geoRelations[$relation->getStart()->getId()])) {
			$this->geoRelations[$relation->getStart()->getId()] = array();
		} else if (isset($this->geoRelations[$relation->getStart()->getId()][$relation->getValue()])) {
			return false;
		}
		$this->relations[$relation->getStart()->getId()][$relation->getValue()]= true;
		$dbrel = $this->relRepo->findOneBy(array('startNode' => $relation->getStart(), 'geometryvalue' => $relation->getValue()));
		if($dbrel !== null) {
			return false;
		}
		return true;
	}

	private function validateRelation(Relation $relation) {
		if(! isset($this->relations[$relation->getStart()->getId()])) {
			$this->relations[$relation->getStart()->getId()] = array();
		} else if (isset($this->relations[$relation->getStart()->getId()][$relation->_getValue()])) {
			return false;
		}
		$this->relations[$relation->getStart()->getId()][$relation->_getValue()]= true;
		$dbrel = $this->relRepo->findOneBy(array('startNode' => $relation->getStart(), 'value' => $relation->_getValue()));
		if($dbrel !== null) {
			return false;
		}
		return true;
	}
}
