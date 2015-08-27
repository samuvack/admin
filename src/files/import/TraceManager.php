<?php

use \Doctrine\ORM\EntityManager;
class TraceManager {
	private $em;
	private $columnConfig;
	private $nodeDomain;
	public function __construct(EntityManager $em, array $columnConfig, FileParser $fileParser ) {
		$fileParser->_setTraceManager(this);
		$this->em = $em;
		$this->columnConfig = array(
			'trace_name' => 2,
			'context_name' => 6,
			'structure_name' => 9
		);
		$this->nodeDomain = new NodeDomain($em);
	}

	public function handle(array $row) {

	}
}
