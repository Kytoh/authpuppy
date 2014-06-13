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
 * Create connection policies table and add connection fields to the connection table
 *
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */

class Addconnectionfields extends Doctrine_Migration_Base
{
  public function up()
  {

    $this->createTable('ap_connection_policies', array(
      'id' => array('type' => 'integer', 'length' => 11, 'autoincrement' => true, 'primary' => true),
      'type' => array('type' => 'string', 'length' => '20', 'notnull' => true),
      'scope' => array('type' => 'string', 'length' => '15', 'notnull' => true),
      'auth_type' => array('type' => 'string', 'length' => '255'),
      'auth_sub_type' => array('type' => 'string', 'length' => '255'),
      'max_incoming' => array('type' => 'integer', 'length' => 11),
      'max_outgoing'=> array('type' => 'integer', 'length' => 11),
      'max_total' => array('type' => 'integer', 'length' => 11),
      'max_duration' => array('type' => 'string', 'length' => '50'),
      'time_window' => array('type' => 'string', 'length' => '50'),
      'expiration' => array('type' => 'string', 'length' => '50'),
      'identity_and_machine' => array('type' => 'boolean'),
    )); 
   
    $this->addColumn('connections', 'max_total_data', 'integer', 8, array(
          'default' => 0,
          'length' => 8,
             ));
    $this->addColumn('connections', 'disconnect_at', 'timestamp');
  }

  public function down()
  {
    $this->dropTable('ap_connection_policies');
    $this->removeColumn('connections', 'max_total_data');
    $this->removeColumn('connections', 'disconnect_at');
  }
}
