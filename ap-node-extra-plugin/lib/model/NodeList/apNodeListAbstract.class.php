<?php 

/**
 * apNodeListAbstract
 * 
 * Abstract class for all nodes list
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

abstract class NodeListAbstract  {
  protected $nodes = array();
  
  protected $fields = array('name', 'gw_id', 'deployment_status', 'civic_number', 'street_name', 'city', 'province', 
      'country', 'postal_code', 'latitude', 'longitude', 'isOnline', 'public_phone_number', 'mass_transit_info');
  
  protected $parameters = array();
  
  
  public function __construct($parameters = array()) {
      
    if (!isset($parameters['onlyDeployed'])) $parameters['onlyDeployed'] = true;
    
    $this->getNodeList($parameters['onlyDeployed']);
  }
  
  /**
   * Gets the array of nodes to return
   * @param boolean $onlyDeployed  optional default true: only deployed nodes will be returned
   */
  protected function getNodeList($onlyDeployed = true) {
    $nodetbl = Doctrine::getTable('Node');
    
    // For backward compatibility with version of core 0.1.1
    if (!method_exists($nodetbl, 'getNodesByStatuses')) {
      $method = 'getActiveNodes';
      $statuses = array();
      if ($onlyDeployed) {
        $method = "getDeployedNodes";
      }
      $this->nodes = $nodetbl->$method();
    } else {
      $statuses = array();
      if ($onlyDeployed) {
        $statuses[] = NodeTable::DEPLOYED;
      }
      if (apNodeExtraMain::getPlugin()->getConfigValue('show_unmanaged_nodes_on_map', false)) {
        $statuses[] = NodeTable::NON_WIFIDOG_NODE;
      }
      $this->nodes = $nodetbl->getNodesByStatuses($statuses);
    }
    return $this->nodes;
  }
  
  
  /**
   * This function generates an array of node information that will be outputted in a give format
   */
  public function generateList() {
    $nodesArr = array();
    $fields = $this->fields;
    
    // For each node selected
    foreach ($this->nodes as $node) {
      $nodearr = $node->toArray();
      $thisnode = array();
      foreach ($fields as $field) {
        // The field is either an attribute of the node, so it would be set in nodearr
        if (isset($nodearr[$field]))
          $thisnode[$field] = $nodearr[$field];
          
        // It can also be a method returning a value
        elseif (method_exists($node, $field)) {
          $thisnode[$field] = $node->$field();
          
        // Or something unknown
        } else {
          $thisnode[$field] = '';
        }
      }
      $nodesArr[] = $thisnode;
    }
    return $nodesArr;
  }
  
  abstract public function getOutput() ;

}
  