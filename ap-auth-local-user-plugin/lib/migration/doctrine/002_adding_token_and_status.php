<?php
class AddingTokenAndStatus extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('ap_user', 'validation_token', 'string', 40, array('length' => '40'));
    $this->addColumn('ap_user', 'status', 'integer', 1, array('length' => '1', 'default' => '-1'));
  }

  public function down()
  {
    $this->removeColumn('ap_user', 'validation_token');
    $this->removeColumn('ap_user', 'status'); 
  }
}
