<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 07/09/15
 * Time: 10:35
 */

namespace MyApp\Files\Import;


class CSVParser extends FileParser {

	/**
	 * Called when parsing the file, define file-type based logic here
	 */
	protected function run() {
		$inputFileName = $this->filename;
		if(($handle = fopen($inputFileName, "r")) === false)
			return;
		while($this->continueParsing && ($data = fgetcsv($handle)) !== false) {
			$this->streamLine($data);
		}
	}
}
