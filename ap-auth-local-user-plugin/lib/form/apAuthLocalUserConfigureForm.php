<?php

/**
 * apAuthLocalUserForgotPasswordForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apAuthLocalUserConfigureForm extends apPluginManagerConfigurationForm
{
  protected $namespace = 'apAuthLocalUserConfigure';
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->widgetSchema['allow_signup'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['allow_signup'] = new sfValidatorPass();
    
    $this->widgetSchema['enter_email_twice'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['enter_email_twice'] = new sfValidatorPass();
    
    $this->widgetSchema['allow_delete_users'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['allow_delete_users'] = new sfValidatorPass();
    
    $this->widgetSchema['list_paging'] = new sfWidgetFormInputText();
    $this->validatorSchema['list_paging'] = new sfValidatorInteger();
    
    $this->widgetSchema['username_case_sensitive'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => 'Are usernames case sensitive?'));
    $this->widgetSchema->setHelp('username_case_sensitive', 'This option is only available if your database search is case sensitive (like Postgresql).');
    $this->validatorSchema['username_case_sensitive'] = new sfValidatorPass();
    
    $this->widgetSchema['one_connection_per_user'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1, 'label' => 'Allow only one connection per username?'));
    $this->widgetSchema->setHelp('one_connection_per_user', 'If checked, when a user logs in, all previous connections are automatically expired.');
    $this->validatorSchema['one_connection_per_user'] = new sfValidatorPass();
    
    $this->widgetSchema['validate_by_email'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['validate_by_email'] = new sfValidatorPass();
    
    $this->widgetSchema['authenticator_name'] = new sfWidgetFormInputText(array('label' => "Authenticator name"));
    $this->widgetSchema->setHelp('authenticator_name', 'Title that will describe this authenticator.');
    $this->validatorSchema['authenticator_name'] = new sfValidatorString();
    
    $this->widgetSchema['validation_warning'] = new sfWidgetFormTextarea(array('label' => "Validation warning"));
    $this->widgetSchema->setHelp('validation_warning', 'If validation by email is enabled, this text accompanies a checkbox on the signup form to warn to user to check his email');
    $this->validatorSchema['validation_warning'] = new sfValidatorString();
    
    $this->widgetSchema['validation_message'] = new sfWidgetFormTextarea(array('label' => "Validation text"));
    $this->widgetSchema->setHelp('validation_message', 'If validation by email is enabled, this is the text that will be sent to the users.  The location of the validation url is indicated by the "%url%" tag');
    $this->validatorSchema['validation_message'] = new sfValidatorString();
    
    $this->widgetSchema['new_password_message'] = new sfWidgetFormTextarea(array('label' => "New password text"));
    $this->widgetSchema->setHelp('new_password_message', 'Message that will be sent along with the password when users request a new password.  The password and username location is indicated by the "%password%" and "%username%" tags respectively');
    $this->validatorSchema['new_password_message'] = new sfValidatorString();
    
    $this->widgetSchema['text_before'] = new sfWidgetFormTextarea(array('label' => "Text before"));
    $this->widgetSchema->setHelp('text_before', 'Html text to display before the login form');
    $this->validatorSchema['text_before'] = new sfValidatorString(array('required' => false));
    
    $this->widgetSchema['text_after'] = new sfWidgetFormTextarea(array('label' => "Text after"));
    $this->widgetSchema->setHelp('text_after', 'Html text to display after the login form');
    $this->validatorSchema['text_after'] = new sfValidatorString(array('required' => false));
  }
  
  public function getPartial() {
    return "apAuthLocalUserLogin/formConfigure";
  }
  
  public function findDefaults() {
    $this->setDefaults(array('allow_signup' => $this->plugin->getConfigValue('allow_signup', true),
                           'enter_email_twice' => $this->plugin->getConfigValue('enter_email_twice', true),
                          'allow_delete_users' => $this->plugin->getConfigValue('allow_delete_users', false),
    					  'list_paging' => $this->plugin->getConfigValue('list_paging', 25),
                          'username_case_sensitive' => $this->plugin->getConfigValue('username_case_sensitive', false),
                          'one_connection_per_user' => $this->plugin->getConfigValue('one_connection_per_user', false),
                          'validate_by_email' => $this->plugin->getConfigValue('validate_by_email', true),
                          'validation_warning' => $this->plugin->getConfigValue('validation_warning', 'You must validate your account by checking your email and clicking on the link'),
                          'authenticator_name' => $this->plugin->getConfigValue('authenticator_name', "Local network user authentication"),
                          'validation_message' => $this->plugin->getConfigValue('validation_message',"You must validate your account.  To do so, please click on the following link: %url%"),
    					  'new_password_message' => $this->plugin->getConfigValue('new_password_message',"Hi %username%,\n\nThis e-mail is being sent because you requested a new password.\n\nYour new password is: %password%"),
                          'text_before' => $this->plugin->getConfigValue('text_before', ""),
                           'text_after' => $this->plugin->getConfigValue('text_after', "")));
  }

}