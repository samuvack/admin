<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 05/08/15
 * Time: 09:11
 */

namespace MyApp\Entities;


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

}
