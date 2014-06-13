<?php

/**
 * apSimpleNetworkSigninForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apSimpleNetworkSigninForm extends BaseFormDoctrine
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    
    $this->validatorSchema->setPostValidator(new apSimpleNetworkUserValidator(array('node' => $this->getObject())));         
  }
  
  public function getModelName() {
      return 'Node'; 
  }
  
}