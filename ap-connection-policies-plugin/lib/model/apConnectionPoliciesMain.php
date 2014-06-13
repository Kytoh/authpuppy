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
 * apConnectionPoliciesMain
 * 
 * This class implements the function that hook this plugin to events
 * 
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */

class apConnectionPoliciesMain {
  protected static $_plugin; 
  
  /**
   * Returns the plugin object associated with this plugin
   * @return apPlugin
   */
  public static function getPlugin() {
    if (is_null(self::$_plugin))
      self::$_plugin = apPlugin::getPlugin('apNodeExtraPlugin');
    return self::$_plugin;
  }
  
  /**
   * Adds the menu
   */
  public static function getMenu(sfEvent $event, $menus) {
    $menus["Network Policies"] = array();
    $menus["Network Policies"][] = array('text' => 'Manage network policies', 'link' => 'ap_connection_policies_index', 'privilege' => 'admin');
    $menus["Network Policies"][] = array('text' => 'View connection status', 'link' => 'ap_connection_policies_status', 'privilege' => 'Logged');
    return $menus;
  }
  
  public static function initiatingConnection(sfEvent $event, $connection) {
    // Get policies that match the current connection
    $policies = Doctrine_Core::getTable('apConnectionPolicies')->getMatchingPolicies($connection);

    // Ask other plugins if this list of matching policies need to be filtered
    $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    $fevent = $dispatcher->filter(new sfEvent($event->getSubject(), 'policies.getting_applicable', array('connection' => $connection)), $policies);          
    $policies = $fevent->getReturnValue();

    $mostrestrictive = array('disconnect_at' => null, 'max_total_data' => null);
    foreach ($policies as $policy) {
      $values = $policy->calculateLimit($connection, $connection->getNodeId());

      // Add this applicable policy
      $applicable = new apApplicablePolicies();
      $applicable->setConnectionId($connection->getId());
      $applicable->setPolicyId($policy->getId());
      $applicable->setTotalData(isset($values['total_data'])?$values['total_data']:0);
      $applicable->setThisTotalData($values['max_total_data']);
      $applicable->setDisconnectAt(!is_null($values['disconnect_at']) ? date('Y-m-d H:i:s', $values['disconnect_at']):null);
      $applicable->save();
      
      if (!is_null($values['disconnect_at'])) {
        // This is the most restrictive policy so far
        if ( is_null($mostrestrictive['disconnect_at']) || ($mostrestrictive['disconnect_at'] > $values['disconnect_at']) ) {
          $mostrestrictive['disconnect_at'] = $values['disconnect_at'];
        }
      }
     if (!is_null($values['max_total_data'])) {
        // This is the most restrictive policy so far
        if (is_null($mostrestrictive['max_total_data']) || ($mostrestrictive['max_total_data'] > $values['max_total_data']) ) {
          $mostrestrictive['max_total_data'] = $values['max_total_data'];
        }
      }
    }
    $connection->setMaxTotalData($mostrestrictive['max_total_data']);
    $connection->setDisconnectAt((!is_null($mostrestrictive['disconnect_at']) ? date('Y-m-d H:i:s', $mostrestrictive['disconnect_at']):null));
    return $connection; 
  }
  
  public static function settingUpConnection(sfEvent $event, $connection) {
    $connection->hasColumn('max_total_data', 'float', null, array(
             'default' => null
        ));
    $connection->hasColumn('disconnect_at', 'timestamp');
    $connection->hasMany('apApplicablePolicies as Policies', array(
             'local' => 'id',
             'foreign' => 'connection_id'));
            
    return $connection; 
  }
  
  public static function verifyConnection(sfEvent $event, $retvals) {
    $connection = $event['connection'];
    if (!is_null($connection)) {
        if ( !is_null($connection->getMaxTotalData()) && ($connection->getIncoming() + $connection->getOutgoing()) > $connection->getMaxTotalData()) {
          $retvals['auth'] = 0; 
          $retvals['messages'] = "| You've used all your bandwidth for this session.";
        }
        if ( !is_null($connection->getDisconnectAt()) && (time() > strtotime($connection->getDisconnectAt()))) {
          $retvals['auth'] = 0; 
          $retvals['messages'] = "| Your connection has expired.";
        }
    }
    return $retvals;
  }
  
  public static function displayStatus(sfEvent $event, $retvals) {
    $identity = $event['identity'];
    if (!is_null($identity)) {
      $policies = Doctrine::getTable('apApplicablePolicies');
      $status = $policies->getForIdentity($identity);
      foreach ($status as $s) {
        $retvals[] = $s;
        
      }
    }
    return $retvals;
  }
  
  public static function portalPage(sfEvent $event, $result) {
    $identity = $event->getSubject()->getUser()->getAttribute('identity');
    
    if (!is_null($identity)) {
      // There is an identity connected, so we show the status for this identity
      $policies = Doctrine::getTable('apApplicablePolicies');
      $statuspolicies = $policies->getForIdentity($identity);
      if (!empty($statuspolicies)) {
        $event->getSubject()->statuses = $statuspolicies;
        
        $showpage = false;
        foreach ($statuspolicies as $statuspolicy) {
          if ($statuspolicy->getPolicy()->getStatusDisplay()) {
            $showpage = true;
            continue;
          }
        }
        
        if ($showpage) {
          // Because the setTemplate function of symfony does not work with modules in plugin, we must do a little something ugly here       
          $result['template'] = sfConfig::get('sf_plugins_dir').'/apConnectionPoliciesPlugin/modules/apConnectionPolicies/templates/status';
        }
      }
    }
    
    return $result;
  }
  
}
