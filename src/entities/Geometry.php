<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/08/15
 * Time: 09:11
 */

namespace MyApp\Entities;
//use Doctrine\Common\Collections\ArrayCollection;
use MyApp\Values\RenderableValue;
use MyApp\FormTypes\GeometryType;


/**
 * @Entity
 * @Table(name="geometries")
 */
class Geometry implements RenderableValue
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    // TODO: make geometry Type

    /** @Column(type="geometry") */
    private $geom;

    function getGeom()
    {
        return $this->geom;
    }

    function setGeom($new_geom)
    {
        $this->name = (string) $new_geom;
    }
    /*public function __toString()
    {
        //TODO register class as service to access doctrine
        $em = $this->em;
        $qb = $em->createQueryBuilder('n');
        $qb->select('st_astext(geom)')
            ->from('geometries', 'g')
            ->where('g.id = ?1')
            ->setParameter(1,$object->id);

        return $qb->getQuery()->getResult();
    }*/





    /**
     * @return String simple string for use in e.g. the graph
     */
    public function __toString() {
        //TODO adapt function to represent geometery in readable format usign postgis function st_astext, ~similar to above
        return $this->geom;
    }

    /**
     * Get FormType
     */
    public function getFormType(\Silex\Application $app) {
        return new GeometryType($app);
    }

    /**
     * Extended view, for detailed representation
     */
    public function render(\Twig_Environment $env, array $params) {
        $params = array_merge(array('value'=> $this), $params);
        $env->display("values/geo.twig", $params);
    }

}
