<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 03/09/15
 * Time: 14:38
 */

namespace MyApp\Files\Import;


interface Manager {
	public function endOfStream();
	public function handle(array $row);
}
