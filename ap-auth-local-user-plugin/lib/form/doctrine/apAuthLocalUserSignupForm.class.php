<?php

/**
 * PluginapUserForm
 * 
 * The form the unauthenticated user will see when signing up for the service
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apAuthLocalUserSignupForm extends apUserForm
{
  public function setup()
  {
    parent::setup();
    $this->useFields(array('username', 'password', 'email', 'username_lower'));
    
    $this->validatorSchema['username']->addOption('trim', true);
    
    $this->validatorSchema['password'] = new sfValidatorString(array('min_length'=>8, 'max_length' => 50),
           array('max_length' => 'password is too long (%max_length% characters max).', 'min_length' => 'password is too short (%min_length% characters min).'));
    
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
        
    // If the option has been set to enter email twice when signing up, add a new email field
    if (apAuthLocalUserMain::getPlugin()->getConfigValue('enter_email_twice', true)) {
        $this->widgetSchema['email_again'] = new sfWidgetFormInputText();
        $this->validatorSchema['email_again'] = clone $this->validatorSchema['email'];
        $this->widgetSchema->moveField('email_again', 'after', 'email');
        $this->mergePostValidator(new sfValidatorSchemaCompare('email', sfValidatorSchemaCompare::EQUAL, 'email_again', array(), array('invalid' => 'The two emails you entered do not match.')));
    
    }
    
    if (apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('validate_by_email', true)) {
        $validatewarning = apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('validation_warning', 'You must validate your account by checking your email and clicking on the link');
        if ($validatewarning != '') {
            $this->widgetSchema['warning'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => str_replace("\n", '<br/>', $validatewarning)));
            $this->validatorSchema['warning'] = new sfValidatorInteger(array('min' => 1, 'max' => 1), array('required' => 'You must read and check this checkbox'));
        }
    }
    
    $this->widgetSchema['remember_me'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['remember_me'] = new sfValidatorPass();

    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
    $this->disableLocalCSRFProtection();
  }
  
  public function rememberMe($apUser) {
    $values = $this->getValues();
    if ($values['remember_me'] == 1) {
      $apUserCookie = new apUserRememberMe();
      $apUserCookie->setUserId($apUser->getId());
      $apUserCookie->save();
    }
 
  }
}
