<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | =============================                                          |
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
 * Add the applicable policies table to save all policies valid for a connection
 *
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */

class Applicablepolicies extends Doctrine_Migration_Base
{
  public function up()
  {

    $this->createTable('ap_applicable_policies', array(
      'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'primary' => true),
      'connection_id' => array('type' => 'integer', 'length' => 11, 'notnull' => true),
      'policy_id' => array('type' => 'integer', 'length' => 11, 'notnull' => true),
      'this_total_data' => array('type' => 'integer', 'length' => 8),
      'total_data' => array('type' => 'integer', 'length' => 8),
      'disconnect_at' => array('type' => 'timestamp',
              'length' => 25),
      'status_message' => array('type' => 'string', 'length' => 1000),
    )); 
    
    $this->addColumn('ap_connection_policies', 'status_message', 'string', 1000, array(
          'length' => 1000,
             ));
    
    $this->addIndex("ap_applicable_policies", "policy_conn_idx", array(
            'fields' => array('connection_id'),
      ));
   
  }

  public function down()
  {
    $this->dropIndex('policy_conn_idx');
    $this->dropTable('ap_applicable_policies');

  }
}

