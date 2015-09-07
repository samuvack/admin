<?php

use Phinx\Migration\AbstractMigration;

class UserSystemFix extends AbstractMigration
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
        $users = $this->table('users');
        $users->renameColumn('isEnabled','is_enabled')
            ->renameColumn('confirmationToken','confirmation_token')
            ->renameColumn('timePasswordResetRequested','time_password_reset_requested')
            ->addColumn('name','string', array('limit'=> 100))
            ->changeColumn('username', 'string', array('limit' => 100, 'null'=>true))
            ->update();
    }

    public function down() {
        $users = $this->table('users');
        $users->renameColumn('is_enabled', "isEnabled")
            ->renameColumn('confirmation_token', "confirmationToken")
            ->renameColumn('time_password_reset_requested',"timePasswordResetRequested")
            ->removeColumn('name')
            ->changeColumn('username', 'string', array('limit' => 100))
            ->save();
    }
}
