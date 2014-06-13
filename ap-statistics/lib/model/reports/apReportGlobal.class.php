<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | ===============================                                        |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+

/**
 * apReportGlobal
 * Report to get sums and global statistics
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportGlobal extends apReportBase 
{
  protected $formname = "apReportFormGlobal";
  protected $tablename = 'Connection';
  protected $fieldlist = array();
  
  
  /**
   * A custom report can be generated if a valid range has been set
   * @return boolean
   */
  protected function canGenerate() {
    $values = $this->form->getValues();
    if (isset($values['groupby'])) {
      return true;
    }
    return false;
  }
  
  protected function buildQuery() {
    $values = $this->form->getValues();
    $table = $this->getTable();

    $q = $table->createQuery('c');
    $q->setHydrationMode(Doctrine_Core::HYDRATE_SCALAR);
    $q = $table->addDateRangeQuery($q, $values['range']['from'] . " 0:0:0", $values['range']['to']. " 23:59:59");
    $q = $table->addNodeQuery($q, $values['nodelist']);
       
    switch($values['groupby']) {
      case 'Node':
        $q->addSelect('c.node_id');
        $q->innerJoin('c.NodeRel n')->addSelect('n.name');
        $q->addSelect('count(c.id) as num_connections');
        $q->addSelect("count(distinct c.mac) as num_machines");
        $q->addSelect('count(distinct c.identity) as num_identity');
        $q->groupBy('c.node_id, n.name');
        $this->setFieldList(array("name", 'num_machines', 'num_identity', 'num_connections', 'incoming', 'outgoing'));
        break;
      case 'Identity':
        $q->addSelect('c.identity');
        $q->addSelect('count(c.id) as num_connections');
        $q->addSelect("count(distinct c.mac) as num_machines");
        $q->addSelect("count(distinct c.node_id) as num_nodes");
        $q->groupBy('c.identity');
        $this->setFieldList(array("identity", 'num_machines', 'num_nodes', 'num_connections', 'incoming', 'outgoing'));
        break;
      case 'Mac':
        $q->addSelect('c.mac');
        $q->addSelect('count(c.id) as num_connections');
        $q->addSelect("count(distinct c.identity) as num_identity");
        $q->addSelect("count(distinct c.node_id) as num_nodes");

        $q->groupBy('c.mac');
        $this->setFieldList(array("mac", 'num_identity', 'num_nodes', 'num_connections', 'incoming', 'outgoing'));
        break;
      case 'Authtype':    
        $q->addSelect('c.auth_type');
        $q->addSelect('count(c.id) as num_connections');
        $q->addSelect("count(distinct c.mac) as num_machines");
        $q->addSelect('count(distinct c.identity) as num_identity');

        $q->groupBy('c.auth_type');
        $this->setFieldList(array("auth_type", 'num_machines', 'num_identity', 'num_connections', 'incoming', 'outgoing'));
        break;
      default:
        $q->addSelect('count(c.id) as num_connections');
        $q->addSelect("count(distinct c.mac) as num_machines");
        $q->addSelect('count(distinct c.identity) as num_identity');
        
        $this->setFieldList(array('num_machines', 'num_identity', 'num_connections', 'incoming', 'outgoing'));
        break;
    }
   
    $q->addSelect('sum(c.incoming) as incoming');
    $q->addSelect('sum(c.outgoing) as outgoing');
    $q->orderBy('count(c.id) desc');
    
    return $q;
  }
  
  /**
   * In addition to the default report, we may generate additional reports depending on the query fields
   * @param array of results $results
   * @param string $rptname The name of the report to generate
   * @return array of array of array of rows (many reports can be generated from the same query, though one is usually the cas
   */
  protected function buildReports($results, $rptname = 'detail') {
 
    $rpt = array();
    foreach ($results as $resultrow) {
      $thisrow = array();
      foreach ($resultrow as $k => $v) {
        $thisrow[$k] = new apReportValue($v);
      }
      $rpt[] = $thisrow;
    }
    $this->values = array($rpt);
    $reports = array('Results' => $rpt);
    
    return $reports;
  }
  
  protected function getDefaultFields() {
    return array( 'incoming', 'outgoing');
  }
  
  protected function getFieldList() {
    return $this->fieldlist;
  }
  
  protected function setFieldList($list) {
    $this->fieldlist = $list;
  }
  

  
}