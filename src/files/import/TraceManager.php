<?php

namespace MyApp\Files\Import;

use \Doctrine\ORM\EntityManager;
class TraceManager {
	private $em;
	private $columnConfig;
	private $dao;
	public function __construct(EntityManager $em, array $columnConfig, FileParser $fileParser ) {
		$fileParser->_setTraceManager($this);
		$this->em = $em;
		$this->columnConfig = array(
			'trace_name' => 2,
			'context_name' => 6,
			'structure_name' => 9
		);
		$this->dao = new DAO($em);
	}

	public function handle(array $row) {
		/*if( $row[$this->columnConfig['trace_name']] == null) {
			print_r($row); die();
		}
		if( $row[$this->columnConfig['context_name']] == null) {
			print_r($row); die();
		}
		if( $row[$this->columnConfig['structure_name']] == null) {
			print_r($row); die();
		}*/
		$structure = $trace = $context = null;
		if( $row[$this->columnConfig['trace_name']] !== null)
			$trace = $this->dao->getNode( $row[$this->columnConfig['trace_name']] );
		if($row[$this->columnConfig['context_name']] !== null)
			$context = $this->dao->getNode( $row[$this->columnConfig['context_name']] );
		if($row[$this->columnConfig['structure_name']]  !== null)
			$structure = $this->dao->getNode( $row[$this->columnConfig['structure_name']] );
		if($structure !== null && $context !== null)
			$this->dao->addLink($structure, $context);
		if($trace !== null && $context !== null)
			$this->dao->addLink($context, $trace);
		$this->dao->limitCache();
	}

	public function endOfStream() {
		$this->em->flush();
	}
}
