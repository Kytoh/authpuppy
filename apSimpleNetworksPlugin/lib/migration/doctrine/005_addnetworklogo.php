<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class addnetworklogo extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('ap_simple_network', 'network_logo', 'string', 255, array(
          'length' => 255
             ));
  }

  public function down()
  {
    $this->removeColumn('ap_simple_network', 'network_logo');
  }
  
 
}