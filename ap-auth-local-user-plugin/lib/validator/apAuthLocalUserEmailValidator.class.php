<?php
/**
 * apAuthLocalUserEmailValidator
 * 
 * Validates the username in the database
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id$
 */

class apAuthLocalUserEmailValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('throw_global_error', true);

    $this->addMessage('emailnotfound', 'The email you requested was not found in the database');
  }

  protected function doClean($values)
  {
    $email = isset($values['email_address']) ? $values['email_address'] : '';

    // don't allow to sign in with an empty username
    if ($email)
    {

      $identity = Doctrine_Core::getTable('apUser')
        ->createQuery('l')
        ->where('l.email = ?', $email)
        ->fetchOne();

      if ($identity)
      {
        return array_merge($values, array('identity' => $identity));
      } else {
        throw new sfValidatorError($this, 'emailnotfound');
      }
    } else {

       throw new sfValidatorError($this, 'invalid');
    }

    
  }

  protected function getTable()
  {
    return Doctrine::getTable('apUser');
  }
}
