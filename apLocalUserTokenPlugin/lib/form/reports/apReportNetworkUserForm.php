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
 * apReportFormConnection
 * Form for statistics for a given network
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apReportNetworkUserForm extends apReportFormBase
{

  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->addDateRange("range", true);
    // If the actual user is linked to network, show only the node list for this network
    $user =  sfContext::getInstance()->getUser()->getGuardUser();
    
    $networks = $user->SimpleNetworks->getPrimaryKeys();
    if (count($networks) > 0) {
      $query = Doctrine_Query::create()
        ->select('n.*')
        ->from('Node n')
        ->whereIn('n.simple_network_id', $networks)
        ->orderBy('n.name'); 
      $this->addDoctrineList($name = "nodes", 'Node', array('query' => $query));   
    } else {
        $this->addNodeList();
    }
    $this->addTextField('identity');
    $this->addTextField('user');
    $this->addFieldList('Connection', array('name' => 'node_name', 'duration' => 'duration', 'user_name' => 'user_name'), 'fields');
    $this->widgetSchema->setHelp('fields', 'Select fields you want to display on the report.  If none are selected, a default list will be used');
    $this->widgetSchema['user']->setOption('label', 'User name');
  }
  
  public function getAccessibleNodes() {
    return $this->widgetSchema['nodes']->getChoices();
  }

}