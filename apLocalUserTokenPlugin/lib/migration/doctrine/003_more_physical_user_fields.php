<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class morePhysicalUserFields extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('apPhysicalUser', 'address', 'string', 255, array(
          'length' => 255
             ));
    $this->addColumn('apPhysicalUser', 'city', 'string', 100, array(
          'length' => 100
             ));
    $this->addColumn('apPhysicalUser', 'province', 'string', 100, array(
          'length' => 100
             ));
    $this->addColumn('apPhysicalUser', 'zip', 'string', 50, array(
          'length' => 50
             ));
    $this->addColumn('apPhysicalUser', 'user_status', 'string', 50, array(
          'length' => 50
             ));
    $this->addColumn('apPhysicalUser', 'document_type', 'string', 50, array(
          'length' => 50
             ));

  }

  public function down()
  {
    $this->removeColumn('apPhysicalUser', 'address');
    $this->removeColumn('apPhysicalUser', 'city');
    $this->removeColumn('apPhysicalUser', 'province');
    $this->removeColumn('apPhysicalUser', 'zip');
    $this->removeColumn('apPhysicalUser', 'status');
    $this->removeColumn('apPhysicalUser', 'document_type');
  }
  
 
}