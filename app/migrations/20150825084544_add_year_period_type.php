<?php

use Phinx\Migration\AbstractMigration;

class AddYearPeriodType extends AbstractMigration
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
        $this->execute("UPDATE properties SET datatype ='year_period' WHERE datatype = 'time'");
    }

    public function down() {
        $this->execute("UPDATE properties SET datatype ='time' WHERE datatype = 'year_period'");
    }
}
