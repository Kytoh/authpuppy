<?php

/**
 * apPluginManager actions.
 * 
 * @package    apPluginManagerPlugin
 * @subpackage apPluginManager
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class apStatisticsActions extends sfActions
{

  public function executeIndex(sfWebRequest $request) {
    
    $report_list = array(array('title' => 'Individual user report', 'params' => array('report_name' => 'apReportUser')),
        array('title' => 'Node data report', 'params' => array('report_name' => 'apReportNode')),
        array('title' => 'Connection report', 'params' => array('report_name' => 'apReportConnection')),
        array('title' => 'Global statistics', 'params' => array('report_name' => 'apReportGlobal')),
        /*array('title' => 'Custom report (expert-only)', 'params' => array('report_name' => 'apReportCustom'))*/);
    
    $event = $this->dispatcher->filter(new sfEvent($this, 'report.request_list', array('list' => $report_list)), $report_list) ;
    $this->report_list = $event->getReturnValue();
              
  }
  
  public function executeReport(sfWebRequest $request) {
    $rptname = $request->getParameter('report_name');

    if (!class_exists($rptname)) 
      $rptname = "apReportConnection";
    
    $report = new $rptname($request->getParameterHolder()->getAll());
    $this->form = $report->getForm();
    $generate = true;
    
    // If the request was a post, bind the parameters
    if ($request->isMethod(sfRequest::POST)) {
      $this->form->bind($request->getParameter($this->form->getNameFormat()));
      if (!$this->form->isValid()) {
        $generate = false; 
      } else {
        $values = $this->form->getValues(); 
      }
    }
    if ($generate)
      $this->data = $report->generate();    
    else 
      $this->data = array();
    
    $this->report = $report;
  }

}
