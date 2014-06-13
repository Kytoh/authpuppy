<?php

/**
 * apPhysicalUser form base class.
 *
 * @method apPhysicalUser getObject() Returns the current form's model object
 *
 * @package    authpuppy
 * @subpackage form
 * @author     Frédéric Sheedy
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 24171 2009-11-19 16:37:50Z Kris.Wallsmith $
 */
abstract class BaseapPhysicalUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'first_name'        => new sfWidgetFormInputText(),
      'last_name'         => new sfWidgetFormInputText(),
      'birth_date'        => new sfWidgetFormDateTime(),
      'birth_place'       => new sfWidgetFormInputText(),
      'address'           => new sfWidgetFormInputText(),
      'city'              => new sfWidgetFormInputText(),
      'province'          => new sfWidgetFormInputText(),
      'zip'               => new sfWidgetFormInputText(),
      'user_status'            => new sfWidgetFormInputText(),
      'document_type'     => new sfWidgetFormInputText(),
      'document'          => new sfWidgetFormInputText(),
      'simple_network_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('apSimpleNetwork'), 'add_empty' => true)),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorDoctrineChoice(array('model' => $this->getModelName(), 'column' => 'id', 'required' => false)),
      'first_name'        => new sfValidatorString(array('max_length' => 255)),
      'last_name'         => new sfValidatorString(array('max_length' => 255)),
      'birth_date'        => new sfValidatorDateTime(array('required' => false)),
      'birth_place'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'address'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'city'              => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'province'          => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'zip'               => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'user_status'            => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'document_type'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'document'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'simple_network_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('apSimpleNetwork'), 'required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'apPhysicalUser', 'column' => array('name')))
    );

    $this->widgetSchema->setNameFormat('ap_physical_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'apPhysicalUser';
  }

}
