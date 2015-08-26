<?php

namespace MyApp\Entities\Repositories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;


class RelationRepository extends EntityRepository {
	/*
	 * Find all relations between two nodes
	 * @param $loadNodes Eager-fetch the start and end node (performance)
	 */
	public function findAllNodeToNode($loadNodes = false) {
		$query = $this->getEntityManager()->createQuery('SELECT r FROM :Relation r WHERE r.nodevalue IS NOT NULL');
		if($loadNodes) {
			$query->setFetchMode(":Relation", "startNode", "EAGER");
		}

		return $query->getResult();
	}
}
