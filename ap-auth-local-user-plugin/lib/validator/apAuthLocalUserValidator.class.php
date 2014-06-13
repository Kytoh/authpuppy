<?php
/**
 * apAuthLocalUserValidator
 * 
 * Validates the username in the database
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id$
 */

class apAuthLocalUserValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('username_field', 'username');
    $this->addOption('password_field', 'password');
    $this->addOption('throw_global_error', true);

    $this->addMessage('wrongusername', 'The username or email you entered does not exist');
    $this->setMessage('invalid', 'The password you entered is not valid.');
    $this->addMessage('denied', 'Your access is denied.  Please contact the administrator if you think this is a mistake.');
    $this->addMessage('locked', 'Your access is locked.  Please contact the administrator to see why it is so.');
  }

  protected function doClean($values)
  {
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';
    $password = isset($values[$this->getOption('password_field')]) ? $values[$this->getOption('password_field')] : '';
    
    if (!$this->getOption('required') && ($password == ''))
      return $values;

    $allowEmail = true;
    $method = $allowEmail ? 'retrieveByUsernameOrEmailAddress' : 'retrieveByUsername';

    // don't allow to sign in with an empty username
    if ($username)
    {
       $identity = $this->getTable()->$method($username);
       
       // identity exists?
       if($identity)
       {
          // password is ok?
          if ($identity->checkPassword($password))
          {
            switch ($identity->getStatus()) {
              case apUserTable::AUTHLOCALUSER_STATUS_DENIED: throw new sfValidatorError($this, 'denied'); break;
              case apUserTable::AUTHLOCALUSER_STATUS_LOCKED: throw new sfValidatorError($this, 'locked'); break;
              default: return array_merge($values, array('identity' => $identity));
            }    
          }
          throw new sfValidatorError($this, 'invalid');
       }
       throw new sfValidatorError($this, 'wrongusername');
    }
 
    
  }

  protected function getTable()
  {
    return Doctrine::getTable('apUser');
  }
}
