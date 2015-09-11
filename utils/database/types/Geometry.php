<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 27/08/15
 * Time: 16:36
 */

namespace Utils\Database\Types;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Jsor\Doctrine\PostGIS\Types\GeometryType;


class Geometry extends GeometryType {
	public function convertToPHPValueSQL($sqlExpr, $platform)  {
		// ::geometry type cast needed for 1.5
		return sprintf('ST_AsText(%s::geometry)', $sqlExpr);
	}

	public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform) {
		return sprintf('ST_SetSRID(ST_GeomFromText(%s),4326)', $sqlExpr);
	}

}
