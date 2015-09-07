<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 03/09/15
 * Time: 14:37
 */

namespace MyApp\Files\Import;


class HeaderManager implements Manager {
	private $columns;

	public function endOfStream() {
		return $this->columns;
	}

	public function handle(array $row) {
		$this->columns = $row;
		return false;
	}
}
