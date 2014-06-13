<?php

/**
 * apCreateStealForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apCreateStealForm extends BaseForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    
    // Keep the gw_id of the requested node
    $this->widgetSchema['gw_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['gw_id'] = new sfValidatorPass();
    
    $this->widgetSchema['nodes_list'] = new sfWidgetFormDoctrineChoice(array('model' => 'Node'));
    $this->validatorSchema['nodes_list'] = new sfValidatorDoctrineChoice(array('model' => 'Node'));
    
    $this->widgetSchema->setNameFormat('nodeextra[%s]');
    
  }
  
  public function findDefaults(array $parameters) {
    if (isset($parameters['gw_id'])) {
      $this->setDefaults(array('gw_id' => $parameters['gw_id']));
    }
  }


}