<?php
/**
 * apReportUser
 * Shows a connection report with minimal form for a given user or mac
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportUser extends apReportBase 
{
  protected $formname = "apReportFormUser";
  protected $tablename = 'Connection';
  
  
  /**
   * A connection report can be generated if a valid range has been set
   * @return boolean
   */
  protected function canGenerate() {
    $values = $this->form->getValues();
    if (isset($values['identity'])) {
      return true;
    }
    return false;
  }
  
  protected function buildQuery() {
    $values = $this->form->getValues();
    $table = $this->getTable();
    $fields = $this->getFieldList();
    $q = $table->getQuery();
    $q = $table->addDateRangeQuery($q, $values['range']['from'] . " 0:0:0", $values['range']['to']. " 23:59:59");
    $q = $table->addIdentityQuery($q, $values['identity']);
    $q = $table->addMacQuery($q, $values['mac']);
    $q = $table->addNodeJoinQuery($q);
    return $q;
  }
  
  /**
   * In addition to the default report, we may generate additional reports depending on the query fields
   * @param array of results $results
   * @param string $rptname The name of the report to generate
   * @return array of array of array of rows (many reports can be generated from the same query, though one is usually the cas
   */
  protected function buildReports($results, $rptname = 'detail') {
    $reports = parent::buildReports($results, 'Connection Log');
    
    $values = $this->form->getValues();
    if (!is_null($values['identity']) && ($values['identity'] != '') ) {
      if (!(!is_null($values['mac']) && ($values['mac'] != ''))) {
        //A user was requested so we return the data per mac
        $maccount = array();
        foreach ($results as $result_object) {       
          $mac = $result_object->getMac();
          if (!isset($maccount[$mac]))
            $maccount[$mac] = 0;
          $maccount[$mac] = $maccount[$mac] + 1;
        }
        $report = array();
        foreach ($maccount as $mac => $count) {
          $report[] = array('mac' => new apReportValue($mac), 'count' => new apReportValue($count, array('type' => apReportValue::TYPE_NUMERIC)));
        }
        $reports['Mac Addresses'] = $report;
      }
    } elseif (!is_null($values['mac']) && ($values['mac'] != '') ) {
        //A mac was requested so we return data per user
        $identitycount = array();
        foreach ($results as $result_object) {       
          $identity = $result_object->getIdentity();
          if (!isset($identitycount[$identity]))
            $identitycount[$identity] = 0;
          $identitycount[$identity] = $identitycount[$identity] + 1;
        }
        $report = array();
        foreach ($identitycount as $identity => $count) {
          $report[] = array('identity' => new apReportValue($identity), 'count' => new apReportValue($count,array('type' => apReportValue::TYPE_NUMERIC)));
        }
        $reports['Identities'] = $report;
    } 
    
    return $reports;
  }
  
  protected function getDefaultFields() {
    return array('created_at', 'name', 'mac', 'incoming', 'outgoing', 'duration');
  }
  
  protected function getFieldList() {
    $values = $this->form->getValues();
    return $this->getDefaultFields();
  }
  
}