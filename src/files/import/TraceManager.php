<?php

namespace MyApp\Files\Import;

use \Doctrine\ORM\EntityManager;
use MyApp\Converters\StringConverter;
use \MyApp\Entities\Node;
use \MyApp\Entities\Relation;
use \MyApp\Entities\Property;
class TraceManager {
	private $em;
	private $columnConfig;
	private $dao;
	/*
	 * @param columnConfig:
	 * pass an array mapping the column indexes. Example:
	 *
	 * $columnConfig = array(
	 *		'trace' => array(
	 *			'name' => 2,
	 *			'description' => 4,
	 *			'has date' => 5
	 *		),
	 * 		'context' => array(
     *			'name' => 6,
	 *			'description' => 7,
	 *			'is of type' => array(11, 12, 13)
	 *		),
	 *		'structure' => array(
	 *			'name' => 9,
	 *			'description' => 10
	 *		)
	 *	);
	 *
	 * Only 'name' is required.
	 */
	public function __construct(EntityManager $em, array $columnConfig) {
		$this->em = $em;
		$this->columnConfig = array(
			'trace' => array(
				'name' => 2,
				'description' => 4,
				'has date' => 5
			),
			'context' => array(
				'name' => 6,
				'description' => 7
			),
			'structure' => array(
				'name' => 9,
				'description' => 10
			)
		);
		$this->dao = new DAO($em);
	}

	/*
	 * @param $row The row that was passed to $this->handle()
	 * @param $configName Name of the part of config array (in example above: 'trace'|'context'|'structure'
	 */
	protected function makeNode(array $row, $configName) {
		if(! isset($this->columnConfig[$configName]))
			return null;
		$config = $this->columnConfig[$configName];
		$name = $row[$config['name']];
		if($name === null) { // No name in file means we can't instantiate the node
			return null;
		}
		unset($config['name']);

		$description = null;
		if(isset($config['description'])) {
			// If there is a column for description, fetch it.
			$description = $row[$config['description']];
			unset($config['description']);
		}
		$node = $this->dao->getNode($name, $description);
		// Create relations in the config for this node
		$this->makeNodeRelations($node, $row, $config);

		return $node;
	}

	/*
	 * Get relations from the config for a node
	 */
	protected function makeNodeRelations(Node $node, array $row, $config) {
		foreach($config as $propertyName => $values) {
			$prop = $this->dao->getProperty($propertyName);
			if(is_array($values)) {
				foreach($values as $valueColumn) {
					$this->makeRelation($node, $prop, $row[$valueColumn]);
				}
			} else {
				$this->makeRelation($node, $prop, $row[$values]);
			}
		}
	}

	/*
	 * Create a relation with a value for a node
	 */
	protected function makeRelation(Node $node,Property $prop, $value) {
		if($value === null)
			return;
		$value = StringConverter::getConverter($prop->getDatatype())->toObject($value);
		$rel = new Relation($node, $prop, $value);
		$this->dao->addRelation($rel);
	}

	/*
	 * Convert a row fom the FileParser to (a) node(s) and (a) relation(s).
	 */
	public function handle(array $row) {
		$trace = $this->makeNode($row, 'trace');
		$context = $this->makeNode($row, 'context');
		$structure = $this->makeNode($row, 'structure');
		if($structure !== null && $context !== null)
			$this->dao->addLink($structure, $context);
		if($trace !== null && $context !== null)
			$this->dao->addLink($context, $trace);
		$this->dao->limitCache();
	}

	public function endOfStream() {
		// Flush any remaining entities
		$this->em->flush();
	}
}
