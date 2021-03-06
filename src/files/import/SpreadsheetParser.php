<?php
namespace MyApp\Files\Import;

// This is why you use namespaces
require(__DIR__.'/../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php');

class SpreadsheetParser extends FileParser {
	protected function run() {
		$inputFileName = $this->filename;
		$objReader = \PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load($inputFileName);

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		//  Loop through each row of the worksheet in turn

		// skip header lines
		for ($row = 1; $row <= $highestRow; ++$row){
			if(!$this->continueParsing)
				break;
			// Read a row of data into an array
			// param example: A5:C5
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
				NULL,
				TRUE,
				FALSE);
			$this->streamLine($rowData[0]);
		}
	}
}
