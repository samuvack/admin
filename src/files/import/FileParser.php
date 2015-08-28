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
	 * @return boolean True if the parsing didn't throw exceptions
	 */
	public function parse() {
		try {
			$this->run();
			$this->endOfStream();
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	protected $filename;
	private $traceManager;
	/*
	 * @param $filename the name of the file to parse
	 * @param $tracemanager The manager receiving the outputstream of this parser
	 */
	public function __construct($filename, $traceManager) {
		$this->filename = $filename;
		$this->traceManager = $traceManager;
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
