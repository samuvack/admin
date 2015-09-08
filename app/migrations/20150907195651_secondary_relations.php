<?php

use Phinx\Migration\AbstractMigration;

class SecondaryRelations extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function up() {
        $table = $this->table('secondary_relations');
        $table->addColumn('property','integer')
            ->addForeignKey('property', 'properties', 'id')
            ->addColumn('value','string', array('null'=>true))
            ->addColumn('qualifier', 'integer', array('null'=>true))

            ->addColumn('nodevalue', 'integer', array('null'=>true))
            ->addForeignKey('nodevalue', 'nodes', 'id')
            ->addColumn('geometryvalue', 'integer', array('null'=>true))
            ->addForeignKey('geometryvalue', 'geometries', 'id')
            ->addColumn('parent_relation', 'integer')
            ->addForeignKey('parent_relation', 'relations', 'id')
            ->create();

        $this->execute('ALTER TABLE secondary_relations ADD COLUMN rank ranks');
    }

    public function down() {
        $this->dropTable('secondary_relations');
    }
}
