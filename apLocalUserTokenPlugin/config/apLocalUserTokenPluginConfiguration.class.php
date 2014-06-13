<?php

/**
 * Main configuration for the apLocalUserTokenPlugin
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apLocalUserTokenPluginConfiguration extends sfPluginConfiguration {
 
  const version= "1.0.0-DEV";
  const plugin_name = "apLocalUserTokenPlugin";
 /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {  
       
    $this->dispatcher->connect('menu.build', array('apLocalUserTokenMain', 'getMenu'));
    $this->dispatcher->connect('localuser.set_up', array('apLocalUserTokenMain', 'settingUpLocalUser'));
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

}
