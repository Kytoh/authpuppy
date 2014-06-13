<?php
/**
 * apAuthLocalUserMain
 * 
 * Main class implementing static functions for different calls of the plugin and also a
 *   plugin specific shortcut to the plugin object
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author 		 Philippe April <philippe@philippeapril.com>
 * @version    BZR: $Id$
 */

class apAuthLocalUserMain  {
  protected static $_plugin; 
  
  /**
   * Returns the plugin object associated with this plugin
   * @return apPlugin
   */
  public static function getPlugin() {
    if (is_null(self::$_plugin))
      self::$_plugin = apPlugin::getPlugin('apAuthLocalUserPlugin');
    return self::$_plugin;
  }
  
  /**
   * Adds the menu
   */
  public static function getMenu(sfEvent $event, $result) {
    if (!isset($result["Auth Local User"]))
      $result["Auth Local User"] = array();
    $result["Auth Local User"][] = array('text' => 'Manage local users', 'link' => 'ap_authlocaluser_login', 'privilege' => 'support');
    $result["Auth Local User"][] = array('text' => 'My Account', 'link' => 'ap_authlocaluser_changepwd', 'privilege' => 'Logged_apAuthLocalUser');
    return $result;
  }
  
  /**
   * Returns the authenticator and states of authenticators for this authentication plugin
   */
  public static function getAuthenticatorTypes(sfEvent $event, $result) {  
    $result["apAuthLocalUser"] = array(apAuthLocalUser::SUB_TYPE_VALIDATION => apAuthLocalUser::SUB_TYPE_VALIDATION,
         apAuthLocalUser::SUB_TYPE_FORGOTPASSWORD => apAuthLocalUser::SUB_TYPE_FORGOTPASSWORD,
         apAuthLocalUser::SUB_TYPE_AUTHENTICATED => apAuthLocalUser::SUB_TYPE_AUTHENTICATED);
         
    // Ask other plugins if they have anything to add to this list of auth types for local user
    $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
    $fevent = $dispatcher->filter(new sfEvent($event->getSubject(), 'authlocaluser.getting_auth_types', array('authtypes' => $result)), $result);
    $result = $fevent->getReturnValue();          
    return $result;
  }
  
  public static function settingUpConnection(sfEvent $event, $connection) {
    $connection->hasOne('apUser', array(
             'local' => 'identity',
             'foreign' => 'username',));
    return $connection; 
  }
}