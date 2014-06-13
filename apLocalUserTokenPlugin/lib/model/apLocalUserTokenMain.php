<?php

/**
 * apLocalUserTokenMain
 * 
 * Main class implementing static functions for different calls of the plugin and also a
 *   plugin specific shortcut to the plugin object
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

class apLocalUserTokenMain  {
  protected static $_plugin; 
  
  /**
   * Returns the plugin object associated with this plugin
   * @return apPlugin
   */
  public static function getPlugin() {
    if (is_null(self::$_plugin))
      self::$_plugin = apPlugin::getPlugin('apLocalUserTokenPlugin');
    return self::$_plugin;
  }
  
  /**
   * Adds the menu
   */
  public static function getMenu(sfEvent $event, $result) {
    if (!isset($result["Auth Local User"]))
      $result["Auth Local User"] = array();
    $result["Auth Local User"][] = array('text' => 'New network user', 'link' => 'ap_local_user_token_new', 'privilege' => 'network_manager');
    $result["Auth Local User"][] = array('text' => 'Manage network user', 'link' => 'ap_local_user_token_index', 'privilege' => 'network_manager');
    $result["Auth Local User"][] = array('text' => 'Network statistics', 'link' => 'ap_network_user_stats', 'privilege' => 'network_manager');
    return $result;
  }
  
  public static function settingUpLocalUser(sfEvent $event, $user) {
    $user->hasColumn('physical_user_id', 'integer', 8, array(
             'length' => 8,
        ));
    $user->hasColumn('payment', 'string', 100, array(
             'length' => 100,
        ));
    $user->hasColumn('ticket_notes', 'string', 255, array(
             'length' => 500,
        ));
    $user->hasOne('apPhysicalUser', array(
             'local' => 'physical_user_id',
             'foreign' => 'id'));
    return $user; 
  }
  /*
  public static function settingUpUser(sfEvent $event, $user) {
    $user->hasMany('apNetworkUser', array(
             'local' => 'id',
             'foreign' => 'user_id'));
    $user->hasMany('apSimpleNetwork as SimpleNetworks', array(
             'refClass' => 'apNetworkUser',
             'local' => 'user_id',
             'foreign' => 'simple_network_id'));
    return $user; 
  }
  
  public static function savingNode(sfEvent $event, $node) {
    return $node; 
  }
  
  public static function settingUpNode(sfEvent $event, $node) {
    $node->hasColumn('simple_network_id', 'integer', 8, array(
             'length' => 8,
        ));
    $node->hasOne('apSimpleNetwork', array(
             'local' => 'simple_network_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    return $node; 
  }*/
  
  /**
   * This function adds the geocoding form elements to the node form
   * @param sfEvent $event
   * @param sfForm $form
   */
  /*public static function changingForm(sfEvent $event, $form) {
    // Merge the geocode form into the node form
    $networknodeform = new apNetworkNodeForm($form->getObject());
    $form->mergeForm($networknodeform);
    
    return $form;
  }*/
}