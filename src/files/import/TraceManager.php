<?php

namespace MyApp\Files\Import;

use \Doctrine\ORM\EntityManager;
use MyApp\Converters\StringConverter;
use \MyApp\Entities\Node;
use \MyApp\Entities\Relation;
use \MyApp\Entities\Property;

class TraceManager implements Manager {
	private $em;
	private $columnConfig;
	private $dao;
	private $first = true;
	/*
	 * @param columnConfig:
	 * pass an array mapping the column indexes. Example:
	 *
	 *
	 * Only 'name_column' is required.
	 */
	public function __construct(EntityManager $em, array $columnConfig) {
		$this->em = $em;
		$this->columnConfig = $columnConfig;
		$this->dao = new DAO($em);
	}

	/*
	 * Convert a row fom the FileParser to (a) node(s) and (a) relation(s).
	 * @return boolean Read more lines
	 */
	public function handle(array $row) {
		if($this->first) { // skip first line
			$this->first = false;
			return true;
		}
		foreach($this->columnConfig as $column => $relations) {
			$value = null;
			if(isset($relations['value'])) {
				$value = $relations['value'];
			} else {
				$value = $row[$column];
			}
			if($value === null) {
				continue; //no value
			}
			if($relations['type'] === 'ROOT') {
				$this->dao->getNode($value);
			} else {
				$belongsToName = $row[$relations['belongsTo']];
				if($belongsToName === null)
					continue; // No node to attach this relation to.
				$parent = $this->dao->getNode($belongsToName);

				$property = $this->dao->getPropertyById($relations['type']);
				$relationValue = null;
				if($property->getDataType() == 'node') {
					$relationValue = $this->dao->getNode($value);
					$this->dao->createNodeRelation($parent, $relationValue, $property);
				} else if($property->getDataType() == 'geometry') {
					// TODO
				} else {
					$converter = StringConverter::getConverter($property->getDatatype());
					$relationValue = $converter->toObject($value);
					$relationValue = $converter->toString($relationValue);
					$relation = New Relation($parent, $property, $relationValue);
					$this->dao->addRelation($relation);
				}
			}
		}
		$this->dao->limitCache();
		return true;
	}

	public function endOfStream() {
		// Flush any remaining entities
		$this->em->flush();
		return true;
	}
}
