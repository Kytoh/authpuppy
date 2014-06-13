<?php

/**
 * apReportFormNode
 * Form for connection statistics for given nodes
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportFormGlobal extends apReportFormBase
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->addDateRange("range", true);
    $this->addNodeList("nodelist");
    
    $list = array('0' => '', 'Node' => 'Node', 'Identity' => 'Identity', 'Authtype' => 'Auth type', 'Mac' => 'Mac (machine)');
    $this->widgetSchema['groupby'] = new sfWidgetFormChoice(array('multiple' => false, 'expanded' => false, 'choices' => $list, 'label' => 'Group by'));
    $this->validatorSchema['groupby'] = new sfValidatorChoice(array('required' => false, 'multiple' => false, 'choices' => array_keys($list)));
   
  }

}
