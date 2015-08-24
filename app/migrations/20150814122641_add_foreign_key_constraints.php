<?php

use Phinx\Migration\AbstractMigration;

class AddForeignKeyConstraints extends AbstractMigration
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
        //foreign key and cascade on delete for table relations and column:
        // the startnode
        $this->execute("ALTER TABLE relations ADD CONSTRAINT startnode_fk_constraint FOREIGN KEY (startnode) REFERENCES nodes(id) ON DELETE CASCADE");
        //nodevalue
        $this->execute("ALTER TABLE relations ADD CONSTRAINT nodevalue_fk_constraint FOREIGN KEY (nodevalue) REFERENCES nodes(id) ON DELETE CASCADE");
        //geometry value
        $this->execute("ALTER TABLE relations ADD CONSTRAINT geometryvalue_fk_constraint FOREIGN KEY (geometryvalue) REFERENCES geometries(id) ON DELETE CASCADE");
        //property
        $this->execute("ALTER TABLE relations ADD CONSTRAINT property_fk_constraint FOREIGN KEY (property) REFERENCES properties(id) ON DELETE CASCADE");

    }






    public function down() {
        //remove foreign key constraints fro table relations
        $this->execute("ALTER TABLE relations DROP CONSTRAINT startnode_fk_constraint");
        $this->execute("ALTER TABLE relations DROP CONSTRAINT nodevalue_fk_constraint");
        $this->execute("ALTER TABLE relations DROP CONSTRAINT geometryvalue_fk_constraint");
        $this->execute("ALTER TABLE relations DROP CONSTRAINT property_fk_constraint");
    }
}
