<?php

use Phinx\Migration\AbstractMigration;

class LoggingSystemPart1 extends AbstractMigration
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
        $this->execute("TRUNCATE nodes_log");
        $this->execute("TRUNCATE properties_log");
        $this->execute("TRUNCATE relations_log");

        $log = $this->table('nodes_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','integer')
            ->save();
        $log = $this->table('relations_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','integer')
            ->save();
        $log = $this->table('properties_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','integer')
            ->save();
    }

    public function down(){
        $this->execute("TRUNCATE nodes_log");
        $this->execute("TRUNCATE properties_log");
        $this->execute("TRUNCATE relations_log");
        $log = $this->table('nodes_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','string')
            ->save();
        $log = $this->table('relations_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','string')
            ->save();
        $log = $this->table('properties_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','string')
            ->save();
    }
}
