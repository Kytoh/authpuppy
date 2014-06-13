<?php

/**
 * apAuthLocalUserForgotPasswordForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apAuthLocalUserForgotPasswordForm extends BaseForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    $this->widgetSchema['email_address'] = new sfWidgetFormInput();
    
    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    
    $this->validatorSchema->setPostValidator(new apAuthLocalUserEmailValidator());

    $this->widgetSchema->setNameFormat('apAuthLocalUserForgotpwd[%s]');

  }

}