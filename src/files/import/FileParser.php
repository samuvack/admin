<?php
namespace MyApp\Files\Import;
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/08/15
 * Time: 10:17
 */
abstract class FileParser {
	/**
	 * Called when parsing the file, define file-type based logic here
	 */
	protected abstract function run();

	/*
	 * Call to parse the file
	 */
	public function parse() {
		$this->run();
		$this->endOfStream();
	}

	protected $filename;
	private $traceManager;
	protected $headerLines;
	/*
	 * @param $filename the name of the file to parse
	 * @param $tracemanager The manager receiving the outputstream of this parser
	 */
	public function __construct($filename, $traceManager, $headerLines = 1) {
		$this->filename = $filename;
		$this->traceManager = $traceManager;
		$this->headerLines = $headerLines;
	}

	/*
	 * Call this for every line in your implementation of run()
	 */
	protected function streamLine(array $line) {
		$this->traceManager->handle($line);
	}

	/*
	 * Called at the end of parse()
	 */
	protected function endOfStream() {
		$this->traceManager->endofStream();
	}
}
