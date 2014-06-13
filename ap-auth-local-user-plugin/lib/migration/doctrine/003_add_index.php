<?php
class AddIndex extends Doctrine_Migration_Base
{
  public function up()
  {
      $this->addIndex("ap_user", "username_idx", array(
      		'type' => 'unique',
            'fields' => array('username'),
      ));
      $this->addIndex("ap_user", "email_idx", array(
      		'type' => 'unique',
            'fields' => array('email'),
      ));
  }

  public function down()
  {
      $this->dropIndex('ap_user', 'username_idx');
      $this->dropIndex('ap_user', 'email_idx');
  }
}
