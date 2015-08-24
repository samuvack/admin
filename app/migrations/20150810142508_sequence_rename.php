<?php

use Phinx\Migration\AbstractMigration;

class SequenceRename extends AbstractMigration
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
        $this->execute("ALTER SEQUENCE IF EXISTS statements_id_seq RENAME TO relations_id_seq");
    }

    public function down() {
        $this->execute("ALTER SEQUENCE IF EXISTS relations_id_seq RENAME TO statements_id_seq");
    }
}
