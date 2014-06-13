<?php
/**
 * apReportNode
 * Shows a connection report with minimal form for a given node
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportNode extends apReportBase 
{
  protected $formname = "apReportFormNode";
  protected $tablename = 'Connection';
  
  
  /**
   * A connection report can be generated if a valid range has been set
   * @return boolean
   */
  protected function canGenerate() {
    $values = $this->form->getValues();
    if (isset($values['nodelist'])) {
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
    $q = $table->addNodeQuery($q, $values['nodelist']);
    $q = $table->addNodeJoinQuery($q);
    return $q;
  }
  
  /**
   * Before showing the connection details, we show the report for each node selected
   * @param array of results $results
   * @param string $rptname The name of the report to generate
   * @return array of array of array of rows (many reports can be generated from the same query, though one is usually the cas
   */
  protected function buildReports($results, $rptname = 'detail') {
    $reports = parent::buildReports($results, 'Connection Log');
       
    $reportspernode = array();
    
    $values = $this->form->getValues();
    $nodelist = $values['nodelist'];
    $nodeconns = array();
    
    // Separate the connection data per node
    foreach ($nodelist as $node) {
      $nodeconns[$node] = array();
    }
    foreach ($results as $result_object) {
      $nodeid = $result_object->getNodeId();
      $nodeconns[$nodeid][] = $result_object;
    }
    // Scrunch the data for each node
    foreach ($nodelist as $node) {   
      $nbconns = count($nodeconns[$node]);
      $distinct_ids = 0; $incoming = 0; $outgoing = 0;
      $nodename = "";
      $identities = array();
      foreach ($nodeconns[$node] as $conn) {
        if (!isset($identities[$conn->getIdentity()])) {
          $distinct_ids = $distinct_ids + 1;
          $identities[$conn->getIdentity()] = 0;
        }
        $incoming = $incoming + $conn->getIncoming();
        $outgoing = $outgoing + $conn->getOutgoing();
      }  
      
      if ($nbconns > 0) {
          $aconn = $nodeconns[$node][0];
          $nodename = $aconn->getNodeRel()->getName();
          $reportspernode["Node: $nodename"] = array(array('connections' => new apReportValue($nbconns, array('type' => apReportValue::TYPE_NUMERIC)),
            'distinct_identities' => new apReportValue($distinct_ids, array('type' => apReportValue::TYPE_NUMERIC)),
            'incoming' => new apReportValue($incoming, array('type' => apReportValue::TYPE_SIZE)),
            'outgoing' => new apReportValue($outgoing, array('type' => apReportValue::TYPE_SIZE))));
      }
    }
    
    return array_merge($reportspernode, $reports);
  }
  
  protected function getDefaultFields() {
    return array('created_at', 'name', 'mac', 'incoming', 'outgoing', 'duration');
  }
  
  protected function getFieldList() {
    $values = $this->form->getValues();
    return $this->getDefaultFields();
  }
  
}