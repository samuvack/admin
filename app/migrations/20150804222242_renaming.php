<?php

use Phinx\Migration\AbstractMigration;

class Renaming extends AbstractMigration
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
        $relationsTable = $this->table("statements");
        $relationsTable->renameColumn("startid", "startnode");
        $relationsTable->renameColumn("propertyname", "property");
        $relationsTable->rename("relations");

        $this->table("statements_logging")->rename("relations_log");
        $this->table("properties_logging")->rename("properties_log");
        $this->table("nodes_logging")->rename("nodes_log");
    }
}
