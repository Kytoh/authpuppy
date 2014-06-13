<?php

/**
 * PluginapPhysicalUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7380 2010-03-15 21:07:50Z jwage $
 */
abstract class PluginapPhysicalUser extends BaseapPhysicalUser
{
    public function setUp()
    {
        parent::setUp();

        $this->hasMany('apUser', array(
             'local' => 'id',
             'foreign' => 'physical_user_id'));
    }
    
  public function createTicket($values) {
    $apUser = new apUser(); 
    
    $username =  $apUser->generateRandomPassword(8, '0123456789');
    $password =  $apUser->generateRandomPassword(8, '0123456789');
    $apUser->setUsername($username);
    $apUser->setPassword($password);
    $apUser->setEmail($username.'@'.$username.'.com');
    $apUser->setLocalUserProfileId($values['local_user_profile_id']);
    $apUser->setPayment($values['payment']);
    $apUser->setTicketNotes($values['ticket_notes']);
    $apUser->setSimpleNetworkId($this->getSimpleNetworkId());
    $apUser->setPhysicalUserId($this->getId());
    $apUser->save();
    return array('apUser' => $apUser, 'password' => $password);
  }
}