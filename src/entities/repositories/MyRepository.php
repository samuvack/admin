<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/09/15
 * Time: 09:31
 */

namespace MyApp\Entities\Repositories;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use MyApp\Entities\Node;


class MyRepository extends EntityRepository {
	public function countBy(array $criteria) {
		$first = true;
		$qb = $this->createQueryBuilder('n');

		$qb->select($qb->expr()->count('n'));
		$sql = $qb->getQuery()->getSQL();
		$params = array();
		foreach($criteria as $key => $value) {
			$keyword = $first?'WHERE':'AND';
			$first = false;
			$sql .= ' '.$keyword.' ? = ?';
			$params[] = $key;
			$params[] = $value;
		}
		$ps = $this->_em->getConnection()->prepare($sql);
		foreach($ps->fetch() as $result) {
			return $result;
		}
	}

	public function count() {
		return $this->countBy(array());
	}
}
