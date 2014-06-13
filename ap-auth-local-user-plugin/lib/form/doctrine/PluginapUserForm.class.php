<?php

/**
 * PluginapUserForm
 * 
 * The user form that will be used to edit users by an administrator
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

abstract class PluginapUserForm extends BaseapUserForm
{
  public function setup()
  {
    parent::setup();
    unset($this['registered_on'], $this['validation_token']);
    
    $this->validatorSchema['username']->addOption('trim', true);
    
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password'] = new sfValidatorAnd(array(
      new sfValidatorRegex(array('pattern' => "/^[A-Za-z0-9]+$/")),
      new sfValidatorString(array('min_length' => 8))
    ));
    $this->validatorSchema['password']->setOption('required', false);
    
  /*  $defaultunameval =  $this->validatorSchema['username'];
    $this->validatorSchema['username'] =  new sfValidatorAnd(array(
      $defaultunameval,
      new sfValidatorDoctrineUnique(array('model' => 'apUser', 'column' => 'username', 'primary_key' => 'id'))
    ));*/
    
    $this->validatorSchema['email'] = new sfValidatorEmail();
    
    $this->widgetSchema['username_lower'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['username_lower'] = new sfValidatorPass();
    
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apUser')->getStatuses(),
      'expanded' => false,
    ));

    $this->validatorSchema['status'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('apUser')->getStatuses()),
    ));
    
    $uniqueuser = new sfValidatorDoctrineUnique(array('model' => 'apUser', 'column' => array('username')), array('invalid' => 'An account with the same username already exists'));
    // If the username is not case sensitive, the username_lower must be unique
    if (!apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('username_case_sensitive', false)) {
      $uniqueuser = new sfValidatorDoctrineUnique(array('model' => 'apUser', 'column' => array('username_lower')), array('invalid' => 'An account with the same username already exists'));
    }
    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'apUser', 'column' => array('email')), array('invalid' => 'An account with the same email already exists')),
        $uniqueuser,
      ))
    );
    
    if (self::$dispatcher) {
      $event = self::$dispatcher->filter(new sfEvent($this, 'localuserform.create', array('user' => $this->getObject(), 'form' => $this)), $this);
    } else {
      $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
      $event = $dispatcher->filter(new sfEvent($this, 'localuserform.create', array('user' => $this->getObject(), 'form' => $this)), $this);
    }
  }
  
}
