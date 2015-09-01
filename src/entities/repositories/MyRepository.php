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
		$qb = $this->createQueryBuilder('n');
		$qb->select($qb->expr()->count('n'));
		foreach($criteria as $key => $value) {
			$qb->andWhere($qb->expr()->eq($qb->getRootAliases( )[0].'.'.$key, $qb->expr()->literal($value)));
		}
		return $qb->getQuery()->getSingleScalarResult();
	}

	public function count() {
		return $this->countBy(array());
	}
}
