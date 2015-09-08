<?php

namespace MyApp\Entities\Repositories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use MyApp\Entities\Node;

class NodeRepository extends MyRepository {
	/*
	 * Find nodes based of tsvector searches
	 * @return Array<Node>
	 */
	public function findByDescription($search_term) {
		$qb = $this->createQueryBuilder('n');
		$qb->where("TS_MATCH_OP(n.descr, plainto_tsquery('english', :term)) = TRUE")
			->setParameter('term', $search_term);
		return $qb->getQuery()->getResult();
	}

	/*
	 * Get all nodes which have a direct link to a Geometry
	 * @return Array<Node>
	 */
	public function getAllGeonodes() {
		$qb = $this->createQueryBuilder('n');
		$qb->innerJoin("n.relations","rel",Join::WITH,
			$qb->expr()->eq("n.id","rel.startNode")
		);
		$qb->where("rel.geometryvalue IS NOT NULL");
		return $qb->getQuery()->getResult();
	}

	/*
	 * Get all nodes based on a property_id and the value of said property
	 * @return Array<Node>
	 */
	function findByPropertyValue($prop_id, $rel_value) {
		$qb = $this->createQueryBuilder('n');
		$qb->innerJoin("n.relations","rel",Join::WITH,
			$qb->expr()->eq("n.id","rel.startNode")
		);

		$propQuery = $this->getEntityManager()->createQuery('SELECT p.datatype FROM :Property p WHERE p.id = ?1');
		$propQuery->setParameter(1,$prop_id);
		$propType = $propQuery->getSingleScalarResult();

		if($propType == 'geometry'){
			$qb->where("rel.property = ?1 AND rel.geometryvalue = ?2");
		}elseif($propType == 'node'){
			$qb->where("rel.property = ?1 AND rel.nodevalue = ?2");
		}else {
			$qb->where("rel.property = ?1 AND lower(rel.value) = lower(?2)");
		}

		$qb->setParameters(array(
			1 => $prop_id,
			2 => $rel_value
		));
		//return $qb->getQuery();
		return $qb->getQuery()->getResult();
	}

	/*
	 * Get all nodes based on the value of a property
	 */
	function findByValue($rel_value){
		$qb = $this->createQueryBuilder('n');
		$qb->innerJoin("n.relations","rel",Join::WITH,
			$qb->expr()->eq("n.id","rel.startNode")
		);
		$qb->where("lower(rel.value) = lower(?1)");
		$qb->setParameters(array(
			1 => $rel_value
		));
		return $qb->getQuery()->getResult();
	}

	/*
	 * Get all nodes in jsonformat containing id and name
	 */
	function findNodesJSON(){
		$qb = $this->createQueryBuilder('n');
		$qb->select('n.id')
		->addSelect('n.name');
		$nodes = $qb->getQuery()->getResult();
		$nodes_json = json_encode($nodes);
		return $nodes_json;
	}

}
