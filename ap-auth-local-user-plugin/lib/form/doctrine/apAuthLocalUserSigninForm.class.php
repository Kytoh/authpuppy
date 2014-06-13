<?php

/**
 * apAuthLocalUserSigninForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    SVN: $Id: pre-alpha$
 */
class apAuthLocalUserSigninForm extends BaseForm
{
  protected $rememberMe = null;
  
  /**
   * @see sfForm
   */
  public function setup()
  {
    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(),
      'password' => new sfWidgetFormInputPassword(array('type' => 'password', 'always_render_empty' => false)),
    ));

    $this->setValidators(array(
      'username' => new sfValidatorString(),
      'password' => new sfValidatorString(),
    ));
    
    $this->widgetSchema['remember_me'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['remember_me'] = new sfValidatorPass();

    $this->validatorSchema->setPostValidator(new apAuthLocalUserValidator());

    $this->widgetSchema->setNameFormat('apAuthLocalUser[%s]');
    
    // Check if a cookie needs to auto-log this user
    $apUserRememberMe = Doctrine_Core::getTable('apUserRememberMe')->findUser();
    $this->rememberMe = $apUserRememberMe;
    if ($apUserRememberMe) {
      $user = $apUserRememberMe->getUser();
      $this->setDefaults(array('username' => $user->getUsername(), 'password' => $user->getPassword(), 'remember_me' => true));
    }
    
    if (self::$dispatcher) {
      $event = self::$dispatcher->filter(new sfEvent($this, 'localusersigninform.create', array('form' => $this, 'node' => $this->getOption('node'))), $this);
    } else {
      $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
      $event = $dispatcher->filter(new sfEvent($this, 'localusersigninform.create', array('form' => $this, 'node' => $this->getOption('node'))), $this);
    }
  }
  
  public function rememberMe($values, $apUser) {
    $rememberMe = $this->rememberMe;
    if ($values['remember_me'] == 1) {
      // If the remember me user does not exist, add it
      if (!$rememberMe) {
        $apUserCookie = new apUserRememberMe();
        $apUserCookie->setUserId($apUser->getId());
        $apUserCookie->save();
      }
    } elseif ($values['remember_me'] == 0) {
      // If the remember user exist, then delete it
      if ($rememberMe) {
        $rememberMe->delete();
      }
    }
  }

  
}