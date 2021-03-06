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
        $log->renameColumn('hid','id');
        $log->update();

        $log = $this->table('properties_log');
        $log->addForeignKey('action_by','users','id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->renameColumn('id', 'property_id');
        $log->renameColumn('hid','id');
        $log->update();

        $log = $this->table('relations_log');
        $log->addForeignKey('action_by','users','id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->renameColumn('id', 'relation_id');
        $log->renameColumn('hid','id');
        $log->renameColumn('propertyname','property_id');
        $log->addForeignKey('property_id','properties', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'));
        $log->addColumn('nodevalue', 'integer',array('null' => true));
        $log->addForeignKey('nodevalue','nodes','id',array('delete'=>'SET_NULL','update'=>'CASCADE'));
        $log->addColumn('geometryvalue',"integer",array('null' => true));
        $log->addForeignKey('geometryvalue','geometries','id',array('delete'=>'SET_NULL','update'=>'CASCADE'));
        $log->update();
    }
}
