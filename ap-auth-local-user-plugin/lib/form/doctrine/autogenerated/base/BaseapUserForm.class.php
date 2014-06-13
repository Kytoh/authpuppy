<?php

/**
 * apUser form base class.
 *
 * @method apUser getObject() Returns the current form's model object
 *
 * @package    authpuppy
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseapUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'username'      => new sfWidgetFormInputText(),
      'password'      => new sfWidgetFormInputText(),
      'email'         => new sfWidgetFormInputText(),
      'registered_on' => new sfWidgetFormDateTime(),
      'validation_token' => new sfWidgetFormInputText(),
      'status'         => new sfWidgetFormInputText(),
      'username_lower'      => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'username'      => new sfValidatorString(array('max_length' => 50)),
      'password'      => new sfValidatorString(array('max_length' => 50)),
      'email'         => new sfValidatorString(array('max_length' => 255)),
      'registered_on' => new sfValidatorDateTime(),
      'validation_token'      => new sfValidatorString(array('max_length' => 40)),
      'status'      => new sfValidatorString(array('max_length' => 50)),
      'username_lower'      => new sfValidatorString(array('max_length' => 50, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ap_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'apUser';
  }

}
