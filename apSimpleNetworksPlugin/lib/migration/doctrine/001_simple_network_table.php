<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class SimpleNetworkTable extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('ap_simple_network', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => 8,
              'autoincrement' => true,
              'primary' => true,
             ),
             'name' => 
             array(
              'type' => 'string',
              'notnull' => true,
              'length' => 50,
             ),
             'address' => 
             array(
              'type' => 'string',
              'length' => 255,
             ),
             'owner' => 
             array(
              'type' => 'string',
              'length' => 255,
             ),
             'email' => 
             array(
              'type' => 'string',
              'length' => 255,
             ),
             'created_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
             ),
             'updated_at' => 
             array(
              'notnull' => true,
              'type' => 'timestamp',
              'length' => 25,
             ),
             ), array(
             'indexes' => 
             array(
              'name_idx' => 
              array(
              'fields' => 
              array(
               0 => 'name',
              ),
              'type' => 'unique',
              ),
             ),
             'primary' => 
             array(
              0 => 'id',
             ),
             ));
    }

    public function down()
    {
        $this->dropTable('ap_simple_network');
    }
}