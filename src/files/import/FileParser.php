<?php
namespace MyApp\Files\Import;
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/08/15
 * Time: 10:17
 */
abstract class FileParser {
	protected $filename;
	private $traceManager;
	public function __construct($filename) {
		$this->filename =$filename;
	}

	public function _setTraceManager(TraceManager $traceManager) {
		$this->traceManager = $traceManager;
	}

	public abstract function start();
	protected function streamLine(array $line) {
		$this->traceManager->handle($line);
	}

	protected function endOfStream() {
		$this->traceManager->endofStream();
	}
}
