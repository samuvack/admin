<?php

use Phinx\Migration\AbstractMigration;

class LoggingSystemPart2 extends AbstractMigration
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
        $log = $this->table('nodes_log');
        $log->addForeignKey('action_by','users','id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->renameColumn('id', 'node_id');
        $log->addForeignKey('node_id', 'nodes', 'id', array('delete'=> 'NO_ACTION', 'update'=> 'CASCADE'));
        $log->renameColumn('hid','id');
        $log->update();

        $log = $this->table('properties_log');
        $log->addForeignKey('action_by','users','id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->renameColumn('id', 'property_id');
        $log->addForeignKey('property_id', 'properties', 'id', array('delete'=> 'NO_ACTION', 'update'=> 'CASCADE'));
        $log->renameColumn('hid','id');
        $log->update();

        $log = $this->table('relations_log');
        $log->addForeignKey('action_by','users','id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->renameColumn('id', 'relation_id');
        $log->addForeignKey('relation_id', 'relations', 'id', array('delete'=> 'NO_ACTION', 'update'=> 'CASCADE'));
        $log->renameColumn('hid','id');
        $log->renameColumn('propertyname','property_id');
        $log->addForeignKey('property_id','properties', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->update();
    }
}
