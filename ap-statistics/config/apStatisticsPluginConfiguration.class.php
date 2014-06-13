<?php

/**
 * Main configuration for the apStatisticsPlugin
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apStatisticsPluginConfiguration extends sfPluginConfiguration {
 
  const plugin_name = "apStatisticsPlugin";
 /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {  
    $this->dispatcher->connect('menu.build', array('apStatisticsMain', 'getMenu'));
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

}
