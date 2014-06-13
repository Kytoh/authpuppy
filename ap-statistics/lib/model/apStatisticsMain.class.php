<?php
/**
 * apStatisticsMain
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

class apStatisticsMain  {
  protected static $_plugin; 
  
  /**
   * Returns the plugin object associated with this plugin
   * @return apPlugin
   */
  public static function getPlugin() {
    if (is_null(self::$_plugin))
      self::$_plugin = apPlugin::getPlugin('apStatisticsPlugin');
    return self::$_plugin;
  }
  
  /**
   * Adds the menu
   */
  public static function getMenu(sfEvent $event, $menus) {
    $menus["Statistics"] = array();
    $menus["Statistics"][] = array('text' => 'Statistics', 'link' => 'ap_statistics_main', 'privilege' => 'support');
    return $menus;
  }
  
}