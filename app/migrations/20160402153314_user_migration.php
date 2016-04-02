<?php

use Phinx\Migration\AbstractMigration;

class UserMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    // public function change()
    // {
        
    // }

    /**
     * Migrate Up.
     */
    public function up()
    {
        /**
         * create table for sentry user
         */
        if(!$this->hasTable('users')){
            $table = $this->table('users');
            // increments id automatically created
            $table->addColumn('email', 'string')
                    ->addColumn('password', 'string')
                    ->addColumn('token', 'string', array('null' => true))
                    ->addColumn('permissions', 'text', array('null' => true))
                    ->addColumn('activated', 'boolean', array('default' => 0))
                    ->addColumn('status', 'integer', array('default' => 0))
                    ->addColumn('activation_code', 'string', array('null' => true))
                    ->addColumn('activated_at', 'timestamp', array('null' => true))
                    ->addColumn('last_login', 'timestamp', array('null' => true))
                    ->addColumn('persist_code', 'string', array('null' => true))
                    ->addColumn('reset_password_code', 'string', array('null' => true))
                    ->addColumn('first_name', 'string', array('null' => true))
                    ->addColumn('last_name', 'string', array('null' => true))
                    ->addColumn('created_at', 'timestamp', array('null' => true))
                    ->addColumn('updated_at', 'timestamp', array('null' => true))
                    ->addIndex(array('activation_code', 'reset_password_code'))
                    ->addIndex(array('email'), array('unique' => true))
                    ->save();
        }

        /**
         * create table for sentry group
         */
        if(!$this->hasTable('groups')){
            $table = $this->table('groups');
            // increments id automatically created
            $table->addColumn('name', 'string')
                    ->addColumn('permissions', 'text', array('null' => true))
                    ->addColumn('created_at', 'timestamp', array('null' => true))
                    ->addColumn('updated_at', 'timestamp', array('null' => true))
                    ->addIndex(array('name'), array('unique' => true))
                    ->save();
        }

        /**
         * create sentry user-group relation
         */
        if(!$this->hasTable('users_groups')){
            $table = $this->table('users_groups', array('id' => false, 'primary_key' => array('user_id', 'role_id')));
            $table->addColumn('user_id', 'integer')
                    ->addColumn('role_id', 'integer')
                    ->addForeignKey('user_id', 'users', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
                    ->addForeignKey('group_id', 'groups', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
                    ->save();
        }

        /**
         * create throttle table
         */
        if(!$this->hasTable('throttle')){
            $table = $this->table('throttle');
            // increments id automatically created
            $table->addColumn('user_id', 'integer', array('signed' => false))
                    ->addColumn('ip_address', 'string', array('null' => true))
                    ->addColumn('attempts', 'integer', array('default' => 0))
                    ->addColumn('suspended', 'boolean', array('default' => 0))
                    ->addColumn('banned', 'boolean', array('default' => 0))
                    ->addColumn('last_attempt_at', 'timestamp', array('null' => true))
                    ->addColumn('suspended_at', 'timestamp', array('null' => true))
                    ->addColumn('banned_at', 'timestamp', array('null' => true))
                    ->addIndex(array('user_id'))
                    ->save();
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('throttle');
        $this->dropTable('users_groups');
        $this->dropTable('groups');
        $this->dropTable('users');
    }
}
