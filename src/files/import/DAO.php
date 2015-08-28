<?php
namespace MyApp\Files\Import;
use \MyApp\Entities\Node;
use \MyApp\Entities\Relation;
use \MyApp\Entities\Property;
class DAO {
	private $nodes = array();
	private $relations = array();
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

	public function getNode($name) {
		if(isset($this->nodes[$name])) {
			return $this->nodes[$name];
		}

		$node = $this->nodeRepo->findOneBy(array('name' => $name));
		if($node === null) {
			$node = new Node($name);
			$this->em->persist($node);
		}

		$this->nodes[$name] = $node;
		return $node;
	}

	/*
	 * @param id (integer) or name (string) of property
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
		$this->em->persist($relation);
		$this->relations[] = $relation;
	}

	public function limitCache() {
		if($this->em->getUnitOfWork()->size() > 100) {
			$this->em->flush();
			$this->em->clear();
			$this->nodes = array();
			$this->relations = array();
			$this->properties = array();
			$this->propertiesById = array();

		}
	}
}
