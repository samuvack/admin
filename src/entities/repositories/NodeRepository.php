<?php

namespace MyApp\Entities\Repositories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use MyApp\Entities\Node;

class NodeRepository extends EntityRepository {
	/*
	 * Find Entity based of tsvector searches
	 */
	public function findByDescription($search_term) {
		$qb = $this->createQueryBuilder('n');
		$qb->where("TS_MATCH_OP(n.descr, plainto_tsquery('english', :term)) = TRUE")
			->setParameter('term', $search_term);
		return $qb->getQuery()->getResult();
	}

	/*
	 * Get all nodes which have a direct link to a Geometry
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
	 */
	function findByPropertyValue($prop_id, $rel_value) {
		$qb = $this->createQueryBuilder('n');
		$qb->innerJoin("n.relations","rel",Join::WITH,
			$qb->expr()->eq("n.id","rel.startNode")
		);
		$qb->where("rel.property = ?1 AND lower(rel.value) = lower(?2)");
		$qb->setParameters(array(
			1 => $prop_id,
			2 => $rel_value
		));
		return $qb->getQuery()->getResult();
	}
}
