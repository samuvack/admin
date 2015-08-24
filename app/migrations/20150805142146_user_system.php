<?php

use Phinx\Migration\AbstractMigration;

class UserSystem extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        // Apparently the SimpleUser service uses integers to store timestamps *puke*
        $users = $this->table("users");
        $users->addColumn("email","string", array("limit" => 100))
            ->addColumn("password","string", array("limit" => 255))
            ->addColumn("salt", "string", array("limit" => 255))
            ->addColumn("roles", "string", array("limit" => 255))
            ->addColumn("time_created", "integer", array("default" => 0, "signed" => false))
            ->addColumn("username", "string", array("limit" => 100))
            ->addColumn("isEnabled", "boolean", array("default" => true))
            ->addColumn("confirmationToken", "string", array("limit" => 100, "null" => true))
            ->addColumn("timePasswordResetRequested", "integer", array("null" => true, "signed" => false))
            ->addIndex("email", array("unique" => true))
            ->addIndex("username", array("unique" => true))
            ->create();

        $fields = $this->table("user_custom_fields",
            array('id' => false, 'primary_key' => array('user_id','attribute'))
        );
        $fields->addColumn("user_id","integer",array("signed" => false))
            ->addColumn("attribute","string", array("limit" => 50))
            ->addColumn("value","string", array("limit" => 255, "null" => true))
            ->addForeignKey("user_id", "users", "id", array("delete" => "CASCADE", "update" => "CASCADE"))
            ->create();
    }
}
