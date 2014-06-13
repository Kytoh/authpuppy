<?php
class InitialTables extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->createTable('ap_user', array(
      'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'primary' => true),
      'username' => array('type' => 'string', 'length' => '50', 'notnull' => true),
      'password' => array('type' => 'string', 'length' => '50', 'notnull' => true),
      'email' => array('type' => 'string', 'length' => '255', 'notnull' => true),
      'registered_on' => array('type' => 'timestamp', 'notnull' => true)
    )); 
  }

  public function down()
  {
    $this->dropTable('ap_user');
  }
}
