<?php
class AddRememberme extends apPluginDoctrineMigrationBase
{
  public function up()
  {
    $this->createTable('ap_user_remember_me', array(
      'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'primary' => true),
      'user_id' => array('type' => 'integer', 'length' => 11, 'notnull' => true),
      'remember_me_cookie' => array('type' => 'string', 'length' => '50', 'notnull' => true)
    )); 
    $this->addIndex("ap_user_remember_me", "remember_me_cookie_idx", array(
            'fields' => array('remember_me_cookie'),
      ));
  }

  public function down()
  {
    $this->dropTable('ap_user_remember_me');
  }
  
}
