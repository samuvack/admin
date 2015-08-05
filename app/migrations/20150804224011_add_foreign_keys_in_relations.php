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
    public function up()
    {

        $relations = $this->table("relations");
        $relations->addColumn("nodevalue","integer",array('null' => true))
            ->addColumn("geometryvalue","integer",array('null' => true))
            ->update();

        // A user auth feature was requested, later on we will use that to enhance logging
        $this->execute("DROP TRIGGER IF EXISTS statements_logging_trigger ON relations CASCADE");
        $this->execute("DROP TRIGGER IF EXISTS nodes_logging_trigger ON nodes CASCADE");
        $this->execute("DROP TRIGGER IF EXISTS properties_logging_trigger ON properties CASCADE");

        $this->execute("UPDATE relations SET nodevalue = CAST(value as INT) WHERE property = ".
            "(SELECT id FROM properties WHERE datatype = 'node') ");
        $this->execute("UPDATE relations SET geometryvalue = CAST(value as INT) WHERE property = ".
            "(SELECT id FROM properties WHERE datatype = 'geometry') ");
    }

    public function down() {
        $this->execute("UPDATE relations SET value = nodevalue WHERE nodevalue IS NOT NULL");
        $this->execute("UPDATE relations SET value = geometryvalue WHERE geometryvalue IS NOT NULL");

        $relations = $this->table("relations");
        $relations->removeColumn("nodevalue")
            ->removeColumn("geometryvalue")
            ->update();
    }
}
