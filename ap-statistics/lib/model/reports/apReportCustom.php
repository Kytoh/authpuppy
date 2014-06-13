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
 * apReportCustom
 * Report that creates a custom query from data the user entered
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportCustom extends apReportBase 
{
  protected $formname = "apReportFormCustom";
  protected $tablename = 'Connection';
  
  
  /**
   * A custom report can be generated if a valid range has been set
   * @return boolean
   */
  protected function canGenerate() {
    $values = $this->form->getValues();
    if (isset($values['range'])) {
      if (isset($values['range']['from']) && isset($values['range']['to'])) 
        return true;
    }
    return false;
  }
  
  protected function buildQuery() {
    $values = $this->form->getValues();
    $table = $this->getTable();

    $q = $table->createQuery('c');
    $q->setHydrationMode(Doctrine_Core::HYDRATE_SCALAR);
    $q->select($values['select']);
    if ($values['where'] != '')
      $q->where($values['where']);
    if ($values['group by'] != '')
      $q->groupBy($values['group by']);
   
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
  

  
}