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
            ->removeColumn('action_time')
            ->addColumn('action_time','timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->save();
        $log = $this->table('relations_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','integer')
            ->removeColumn('action_time')
            ->addColumn('action_time','timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->save();
        $log = $this->table('properties_log');
        $log->removeColumn('action_by')
            ->addColumn('action_by','integer')
            ->removeColumn('action_time')
            ->addColumn('action_time','timestamp', array('default' => 'CURRENT_TIMESTAMP'))
            ->save();

        $this->execute("ALTER SEQUENCE IF EXISTS statements_logging_id_seq RENAME TO relations_log_id_seq");
        $this->execute("ALTER SEQUENCE IF EXISTS nodes_logging_hid_seq RENAME TO nodes_log_id_seq");
        $this->execute("ALTER SEQUENCE IF EXISTS properties_logging_hid_seq RENAME TO properties_log_id_seq");
    }

    public function down(){
        $this->execute("ALTER SEQUENCE IF EXISTS relations_log_id_seq RENAME TO statements_logging_id_seq");
        $this->execute("ALTER SEQUENCE IF EXISTS nodes_log_id_seq RENAME TO nodes_logging_hid_seq");
        $this->execute("ALTER SEQUENCE IF EXISTS properties_log_id_seq RENAME TO properties_logging_hid_seq");

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
