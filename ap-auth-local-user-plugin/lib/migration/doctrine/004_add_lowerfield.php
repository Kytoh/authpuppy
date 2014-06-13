<?php
class AddLowerfield extends apPluginDoctrineMigrationBase
{
  public function up()
  {
    $this->addColumn('ap_user', 'username_lower', 'string', 50, array('length' => '50'));
    $this->addIndex("ap_user", "username_lower_idx", array(
            'fields' => array('username_lower'),
      ));
  }

  public function down()
  {
    $this->removeIndex('ap_user', 'username_lower_idx');
    $this->removeColumn('ap_user', 'username_lower');
  }
  
  public function getScripts() {
    return array("UPDATE ap_user SET username_lower = lower(username)"); 
  }
}
