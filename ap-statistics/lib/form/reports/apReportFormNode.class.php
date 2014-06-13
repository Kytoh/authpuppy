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
class apReportFormNode extends apReportFormBase
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->addDateRange("range", true);
    $this->addNodeList("nodelist");
    $this->validatorSchema["nodelist"]->setOption('required', true);
   
  }

}
