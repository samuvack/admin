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
	 *
	 * 	[trace] => Array
     *   (
     *     [name_column] => 2
     *      [description_column] => 4
            [relations] => Array
                (
                    [0] => Array
                        (
                            [property] => MyApp\Entities\Property Object
                                (
                                    [id:MyApp\Entities\Property:private] => 6
                                    [name:MyApp\Entities\Property:private] => has date
                                    [description:MyApp\Entities\Property:private] =>
                                    [datatype:MyApp\Entities\Property:private] => year_period
                                    [descr:MyApp\Entities\Property:private] => Array
                                        (
                                            [0] => date
                                        )

                                )

                            [column] => 5
                        )

                )

        )

		[context] => Array
			(
				[name_column] => 6
				[description_column] => 7
				[relations] => Array
					(
					)

			)

		[structure] => Array
			(
				[name_column] => 9
				[description_column] => 10
				[relations] => Array
					(
					)

        )
		);
	 *
	 * Only 'name_column' is required.
	 */
	public function __construct(EntityManager $em, array $columnConfig) {
		$this->em = $em;
		$this->columnConfig = $columnConfig;
		$this->dao = new DAO($em);
	}

	/*
	 * @param $row The row that was passed to $this->handle()
	 * @param $configName Name of the part of config array (in example above: 'trace'|'context'|'structure'
	 */
	protected function makeNode(array $row, $configName) {
		if (!isset($this->columnConfig[$configName]))
			return null;
		$config = $this->columnConfig[$configName];
		$name = $row[$config['name_column']];
		if ($name === null) { // No name in file means we can't instantiate the node
			return null;
		}
		unset($config['name_column']);

		$description = null;
		if (isset($config['description_column'])) {
			// If there is a column for description, fetch it.
			$description = $row[$config['description_column']];
			unset($config['description_column']);
		}
		$node = $this->dao->getNode($name, $description);
		// Create relations in the config for this node
		$this->makeNodeRelations($node, $row, $config['relations']);

		return $node;
	}

	/*
	 * Get relations from the config for a node
	 */
	protected function makeNodeRelations(Node $node, array $row, $config) {
		foreach ($config as $rel) {
			$prop = $rel['property'];
			$column = $rel['column'];
			$this->makeRelation($node, $prop, $row[$column]);
		}
	}

	/*
	 * Create a relation with a value for a node
	 */
	protected function makeRelation(Node $node, Property $prop, $value) {
		if ($value === null)
			return;
		$prop = $this->dao->getPersistedProperty($prop);
		if($prop->getDataType() === 'node') {
			$rel = new Relation($node, $prop,'', $value);
			$this->dao->addRelation($rel);
		} else if($prop->getDataType() === 'geometry') {
			$rel = new Relation($node, $prop,'',null, $value);
			$this->dao->addRelation($rel);
		} else {
			$converter = StringConverter::getConverter($prop->getDatatype());
			$value = $converter->toObject($value);
			$value = $converter->toString($value);
			$rel = new Relation($node, $prop, $value);
			$this->dao->addRelation($rel);
		}
	}

	/*
	 * Convert a row fom the FileParser to (a) node(s) and (a) relation(s).
	 */
	public function handle(array $row) {
		$trace = $this->makeNode($row, 'trace');
		$context = $this->makeNode($row, 'context');
		$structure = $this->makeNode($row, 'structure');
		if ($structure !== null && $context !== null)
			$this->dao->createNodeRelation($structure, $context);
		if ($trace !== null && $context !== null)
			$this->dao->createNodeRelation($context, $trace);
		$this->dao->limitCache();
	}

	public function endOfStream() {
		// Flush any remaining entities
		$this->em->flush();
	}
}
