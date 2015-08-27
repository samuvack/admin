<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/08/15
 * Time: 10:17
 */
abstract class FileParser {
	private $filename;
	private $traceManager;
	public function __construct($filename) {
		$this->filename =$filename;
	}

	public function _setTraceManager(TraceManager $traceManager) {
		$this->traceManager = $traceManager;
	}

	protected function streamLine(array $line) {
		$this->traceManager->handle($line);
	}
}
