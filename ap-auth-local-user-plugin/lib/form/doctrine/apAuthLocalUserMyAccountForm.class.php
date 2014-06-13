<?php

/**
 * apAuthLocalUserMyAccountFrom
 * 
 * The form to edit preferences for a user
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apAuthLocalUserMyAccountForm extends apUserForm
{
  public function setup()
  {
    parent::setup();
    $this->useFields(array('username', 'password', 'email', 'username_lower'));
    
    $this->widgetSchema['username'] = new sfWidgetFormInputHidden();
  
    $this->validatorSchema['password'] = new sfValidatorString(array('max_length' => 50, 'required' => false),
           array('max_length' => 'password is too long (%max_length% characters max).'));
    
    $this->widgetSchema['password_again'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    
    $this->widgetSchema['old_password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['old_password'] = clone $this->validatorSchema['password'];
    
    $this->validatorSchema['id'] = new sfValidatorInteger(array('max' => $this->getObject()->getId(), 'min' => $this->getObject()->getId()), array('max' => 'Id has changed, form cannot be saved', 'min' => 'Id has changed, form cannot be saved'));

    $this->widgetSchema->moveField('old_password', 'before', 'password');
    $this->widgetSchema->moveField('password_again', 'after', 'password');

    $this->mergePostValidator(new apAuthLocalUserValidator(array('password_field' => 'old_password', 'required' => false)));
    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array('invalid' => 'The two passwords must be the same.')));
    
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'passwordChangeOk')),  array('invalid' => 'You must enter old password.')));
  }
  
  public function passwordChangeOk($validator, $values) {
    if (($values['password'] != '') && ($values['old_password'] == ''))
      throw new sfValidatorError($validator, 'invalid');
    return $values;
  }
}
