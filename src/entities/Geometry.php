<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/08/15
 * Time: 09:11
 */

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="geometries")
 */
class Geometry
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    // TODO: make geometry Type

    /** @Column(type="text") */
    private $geom;


    public function __toString()
    {
        //TODO register class as service to access doctrine
        $em = $this->em;
        $qb = $em->createQueryBuilder('n');
        $qb->select('st_astext(geom)')
            ->from('geometries', 'g')
            ->where('g.id = ?1')
            ->setParameter(1,$object->id);

        return $qb->getQuery()->getResult();
    }

}
