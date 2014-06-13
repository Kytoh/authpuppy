<?php

/**
 * apPerNodeAuthenticatorForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apNetworkNodeForm extends BaseFormDoctrine
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    
    $this->widgetSchema['simple_network_id'] = new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'apSimpleNetwork', 'label' => 'Network'));
    $this->validatorSchema['simple_network_id'] = new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'apSimpleNetwork', 'required' => false));
         
  }
  
  public function getModelName() {
      return 'Node'; 
  }
  
}