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
 * Add the disconnect reason to connection
 *
 * @package    authpuppy
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */

class ConnfieldsInttofloat extends Doctrine_Migration_Base
{
  public function up()
  {
      $this->changeColumn('ap_connection_policies', 'max_incoming', 'float', '', array(
             ));
      $this->changeColumn('ap_connection_policies', 'max_outgoing', 'float', '', array(
             ));
      $this->changeColumn('ap_connection_policies', 'max_total', 'float', '', array(
             ));
      $this->changeColumn('ap_applicable_policies', 'this_total_data', 'float', '', array(
             ));
      $this->changeColumn('ap_applicable_policies', 'total_data', 'float', '', array(
             ));
      $this->changeColumn('connections', 'max_total_data', 'float', '', array(
             ));
      
  }

  public function down()
  {
      $this->changeColumn('ap_connection_policies', 'max_incoming', 'integer', '', array(
             ));
      $this->changeColumn('ap_connection_policies', 'max_outgoing', 'integer', '', array(
             ));
      $this->changeColumn('ap_connection_policies', 'max_total', 'integer', '', array(
             ));
      $this->changeColumn('ap_applicable_policies', 'this_total_data', 'integer', '', array(
             ));
      $this->changeColumn('ap_applicable_policies', 'total_data', 'integer', '', array(
             ));
      $this->changeColumn('connections', 'max_total_data', 'integer', '', array(
             ));
  }
}
