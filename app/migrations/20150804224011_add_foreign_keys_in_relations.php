<?php

use Phinx\Migration\AbstractMigration;

class AddForeignKeysInRelations extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change() {
        $relations = $this->table("relations");
        $relations->addColumn("nodevalue","integer");
        $relations->addColumn("geometryvalue","integer");
    }
}
