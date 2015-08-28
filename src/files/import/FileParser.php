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
	public function __construct($filename, $traceManager) {
		$this->filename = $filename;
		$this->traceManager = $traceManager;
	}

	public function parse() {
		$this->run();
		$this->endOfStream();
	}

	protected abstract function run();
	protected function streamLine(array $line) {
		$this->traceManager->handle($line);
	}

	protected function endOfStream() {
		$this->traceManager->endofStream();
	}
}
