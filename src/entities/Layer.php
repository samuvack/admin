<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 11/09/15
 * Time: 09:27
 */

namespace MyApp\Entities;

/**
 * @Entity
 * @Table(name="website_layers")
 */
class Layer {
	/**
	 * @Id @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;
	/** @Column(type="text") */
	private $feature_info;

	/** @Column(type="text") */
	private $name;
	/**
	 * @OneToMany(targetEntity="Node", mappedBy="layer", cascade={"all"})
	 **/
	private $nodes;

	public function getName() {
		return $this->name;
	}

	public function getId() {
		return $this->id;
	}

}
