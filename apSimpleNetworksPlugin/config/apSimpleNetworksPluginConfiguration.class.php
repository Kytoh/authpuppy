<?php

/**
 * Main configuration for the apSimpleNetworksPlugin
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apSimpleNetworksPluginConfiguration extends sfPluginConfiguration {
 
  const version= "1.0.0-DEV";
  const plugin_name = "apSimpleNetworksPlugin";
 /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {  
    $this->dispatcher->connect('sfGuardUser.setup', array('apSimpleNetworkMain', 'settingUpUser'));
    $this->dispatcher->connect('menu.build', array('apSimpleNetworkMain', 'getMenu'));
    $this->dispatcher->connect('nodeform.create', array('apSimpleNetworkMain', 'changingForm'));
    $this->dispatcher->connect('node.new', array('apSimpleNetworkMain', 'savingNode'));
    $this->dispatcher->connect('node.update', array('apSimpleNetworkMain', 'savingNode'));
    $this->dispatcher->connect('node.set_up', array('apSimpleNetworkMain', 'settingUpNode'));
    $this->dispatcher->connect('localuser.set_up', array('apSimpleNetworkMain', 'settingUpLocalUser'));
    $this->dispatcher->connect('localusersigninform.create', array('apSimpleNetworkMain', 'signinFormCreate'));
    
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

}
