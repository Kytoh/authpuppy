<?php
class Addgeocode extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('nodes', 'latitude', 'decimal', 16, array(
          'scale' => 6
             ));
    $this->addColumn('nodes', 'longitude', 'decimal', 16, array(
          'scale' => 6
             ));
  }

  public function down()
  {
    $this->removeColumn('nodes', 'latitude');
    $this->removeColumn('nodes', 'longitude');
  }
}
