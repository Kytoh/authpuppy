<?php

/**
 * BaseapUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $username
 * @property string $password
 * @property string $email
 * @property timestamp $registered_on
 * 
 * @method string    getUsername()      Returns the current record's "username" value
 * @method string    getPassword()      Returns the current record's "password" value
 * @method string    getEmail()         Returns the current record's "email" value
 * @method timestamp getRegisteredOn()  Returns the current record's "registered_on" value
 * @method apUser    setUsername()      Sets the current record's "username" value
 * @method apUser    setPassword()      Sets the current record's "password" value
 * @method apUser    setEmail()         Sets the current record's "email" value
 * @method apUser    setRegisteredOn()  Sets the current record's "registered_on" value
 * 
 * @package    authpuppy
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
abstract class BaseapUser extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ap_user');
        $this->hasColumn('username', 'string', 50, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '50',
             ));
        $this->hasColumn('password', 'string', 50, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '50',
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('registered_on', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('validation_token', 'string', 40, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '40',
             ));
        $this->hasColumn('status', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '1',
             'default' => '0',
             ));
       $this->hasColumn('username_lower', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Connection as Connections', array(
             'local' => 'username',
             'foreign' => 'identity'));
    }
}