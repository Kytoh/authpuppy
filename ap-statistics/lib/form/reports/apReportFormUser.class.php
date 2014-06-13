<?php

/**
 * apReportFormUser
 * Form for connection by user type statistics
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportFormUser extends apReportFormBase
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->addDateRange("range", true);
    $this->addTextField('identity');
    $this->addTextField('mac');
    $this->widgetSchema->setHelp('fields', 'Select fields you want to display on the report.  If none are selected, a default list will be used');
    
    $this->validatorSchema->setPostValidator(new apIdOrMacValidator());
  }

}

class apIdOrMacValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('throw_global_error', true);

    $this->addMessage('allempty', 'You must enter either the username or the mac adddress');
  }

  protected function doClean($values)
  {
    $identity = isset($values['identity']) ? $values['identity'] : '';
    $mac = isset($values['mac']) ? $values['mac'] : '';
    
    if ($identity == '' && $mac == '')
      throw new sfValidatorError($this, 'allempty');
    return $values;
  }

}

 