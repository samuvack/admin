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
	private $nodeRepo;
	private $propRepo;
	private $properties = array();
	private $propertiesById = array();
	private $em;
	public function __construct(\Doctrine\ORM\EntityManager $em) {
		$this->nodeRepo = $em->getRepository(':Node');
		$this->propRepo = $em->getRepository(':Property');
		$this->em = $em;
	}

	public function getNode($name, $description) {
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

		$this->addProperty($prop);
		return $prop;
	}

	/*
	 * Add a property object to the maps
	 */
	protected function addProperty(Property $prop) {
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
		$this->addProperty($prop);
		return $prop;
	}

	public function addLink(Node $parentNode, Node $childNode) {
		$prop = $this->getPropertyById(5); // 'is_part_of' property
		$relation = new Relation($parentNode, $prop, null, $childNode);
		$this->addRelation($relation);
	}

	/*
	 * Limit the entities in the entitymanager to avoid OOM-errors
	 */
	public function limitCache() {
		if($this->em->getUnitOfWork()->size() > 500) {
			$this->em->flush();
			$this->em->clear();
			$this->nodes = array();
			$this->relations = array();
			foreach($this->properties as $key=>$property) {
				$merged = $this->em->merge($property);
				$this->properties[$key] = $merged;
				$this->propertiesById[$merged->getId()] = $merged;
			}
		}
	}

	public function addRelation(Relation $relation) {
		if($relation->getProperty()->getDataType() == 'node') {
			if(! isset($this->nodeRelations[$relation->getStart()->getId()])) {
				$this->nodeRelations[$relation->getStart()->getId()] = array();
			}
			$this->nodeRelations[$relation->getStart()->getId()][$relation->getValue()->getId()] = $relation;
		}
		$this->em->persist($relation);
	}
}
