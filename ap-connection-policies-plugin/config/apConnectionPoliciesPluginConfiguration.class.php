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
 * Main configuration for the apConnectionPoliciesPlugin
 *
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */

class apConnectionPoliciesPluginConfiguration extends sfPluginConfiguration {

  /**
    * @see sfPluginConfiguration
    */
  public function initialize()
  {  
    $this->dispatcher->connect('connection.set_up', array('apConnectionPoliciesMain', 'settingUpConnection'));
    $this->dispatcher->connect('connection.first_check', array('apConnectionPoliciesMain', 'initiatingConnection'));     
    $this->dispatcher->connect('menu.build', array('apConnectionPoliciesMain', 'getMenu'));
    $this->dispatcher->connect('connection.status_verification', array('apConnectionPoliciesMain', 'verifyConnection'));
    $this->dispatcher->connect('node.gw_message', array('apConnectionPoliciesMain', 'displayStatus'));
    $this->dispatcher->connect('portalpage.request', array('apConnectionPoliciesMain', 'portalPage'));      
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

}
