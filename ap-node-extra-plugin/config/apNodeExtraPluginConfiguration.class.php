<?php

/**
 * Main configuration for the apNodeExtraPlugin
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apNodeExtraPluginConfiguration extends sfPluginConfiguration {
 
  const version= "1.0.0-DEV";
  const plugin_name = "apNodeExtraPlugin";
 /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {  
    $this->dispatcher->connect('loginpage.node_not_found', array('apNodeExtraMain', 'nodeNotFound'));
    $this->dispatcher->connect('nodeform.create', array('apNodeExtraMain', 'changingForm'));
    $this->dispatcher->connect('node.new', array('apNodeExtraMain', 'savingNode'));
    $this->dispatcher->connect('node.update', array('apNodeExtraMain', 'savingNode'));
    $this->dispatcher->connect('node.set_up', array('apNodeExtraMain', 'settingUpNode'));
    $this->dispatcher->connect('menu.build', array('apNodeExtraMain', 'getMenu'));
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

}
