<?php
namespace Utils\Graph;
use MyApp\Entities\Relation;

/**
 * Created by PhpStorm.
 * User: david
 * Date: 02/09/15
 * Time: 14:05
 */
class GraphData {
	private $idConverter;
	private $nodes = [];
	private $links = [];
	public function addValue($value) {
		$id = sizeof($this->nodes);
		$this->nodes[] = [
			'name' => $value->__toString(),
			'id'=> $id,
			'nodeid' => null
		];
		return $id;
    }

	public function addNode($node){
        if( ! isset($this->idConverter[$node->getId()])) {
			$id = sizeof($this->nodes);
            $this->nodes[] = [
                'name' => $node->getName(),
                'id'=> $id,
                'nodeid' => $node->getId()
            ];
			$this->idConverter[$node->getId()] = $id;
        }
		return $this->idConverter[$node->getId()];
	}

	public function addRelations($relations) {
		// use(&$idConverter, &$graphNodes, &$graphLinks, $addNode, $addValue)
        foreach( $relations as $relation ){
			$this->addRelation($relation);
        }
    }

	public function addRelation(Relation $relation) {
		$targetId = null;
		//startnode is always of type node
		$sourceId = $this->addNode($relation->getStart());
		if($relation->getProperty()->getDatatype() == 'node'){
			$targetId = $this->addNode($relation->getValue());
		} elseif ($relation->getProperty()->getDatatype() == 'geometry') {
			//not added
		} else {
			$targetId = $this->addValue($relation->getValue());
		}
		if(isset($targetId)){
			$this->links[] = [
				'source' => $sourceId,
				'target' => $targetId,
				'pname' => $relation->getProperty()->getName()
			];
		}
	}

	public function getLinks() {
		return $this->links;
	}

	public function getNodes() {
		return $this->nodes;
	}


}
