<?php
/**
 * apNodeExtraMain
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

class apNodeExtraMain  {
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
    $menus["Node Extra"] = array();
    $menus["Node Extra"][] = array('text' => 'Hotspot map', 'link' => 'ap_nodeextra_map');
    return $menus;
  }
  
  public static function savingNode(sfEvent $event, $node) {
    return $node; 
  }
  
  public static function settingUpNode(sfEvent $event, $node) {
    $node->hasColumn('latitude', 'decimal', 16, array(
             'scale' => 6,
        ));
    $node->hasColumn('longitude', 'decimal', 16, array(
             'scale' => 6,
        ));
    return $node; 
  }
  
  /**
   * This function adds the geocoding form elements to the node form
   * @param sfEvent $event
   * @param sfForm $form
   */
  public static function changingForm(sfEvent $event, $form) {
    // Merge the geocode form into the node form
    $geocodeform = new apGeocodeForm($form->getObject());
    $form->mergeForm($geocodeform);
    
    // Connect to the post action for the geocoder
    $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    $dispatcher->connect('nodeform.unknown_post_action', array($geocodeform, 'geocode'));
    return $form;
  }
  
  public static function nodeNotFound(sfEvent $event) {
    if (apNodeExtraMain::getPlugin()->getConfigValue('allow_create_or_steal_nodes', true)) {
      $event->getSubject()->forward('apNodeExtra', 'createsteal');
      return true;
    }
    return false;
  }
  
}