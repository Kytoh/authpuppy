<?php
/**
 * apReportConnection
 * Report that displays connection information 
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportConnection extends apReportBase 
{
  protected $formname = "apReportFormConnection";
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
    $q = $table->addNodeQuery($q, $values['nodes']);
    $q = $table->addAuthTypesQuery($q, $values['auth_types']);
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
    
    if (is_array($values['nodes']) && count($values['nodes']) > 0) {
      // Nodes were selected so we do some node reporting 
      $nodedata = array();
      foreach ($results as $result_object) {
        $nodeid = $result_object->getNodeId();
        if (!isset($nodedata[$nodeid])) {
          $references = $result_object->getReferences();
          $nodeinfo = $references['NodeRel'];
          $nodedata[$nodeid] = array('Node name' =>$nodeinfo->getName(), 'incoming' => 0, 'outgoing' => 0 );
        }
          
        $nodedata[$nodeid]['incoming'] = $nodedata[$nodeid]['incoming'] + $result_object->getIncoming();
        $nodedata[$nodeid]['outgoing'] = $nodedata[$nodeid]['outgoing'] + $result_object->getOutgoing();
      }
      $reports['Node Report'] = $nodedata;
    }
    
    return $reports;
  }
  
  protected function getDefaultFields() {
    return array('name', 'mac', 'auth_type', 'auth_sub_type',  'identity', 'incoming', 'outgoing', 'duration');
  }
  
  protected function getFieldList() {
    $values = $this->form->getValues();
    if (!is_null($values['fields'])) {
      return $values['fields'];
    } else 
      return $this->getDefaultFields();
  }
  
}