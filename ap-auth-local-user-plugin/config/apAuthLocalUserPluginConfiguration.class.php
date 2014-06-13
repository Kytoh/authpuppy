<?php

/**
 * Main configuration for the apAuthLocalUserPlugin
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

class apAuthLocalUserPluginConfiguration extends sfPluginConfiguration {
 
  const version= "1.0.0-DEV";
  const plugin_name = "apAuthLocalUserPlugin";
 /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {  
    $this->dispatcher->connect('connection.set_up', array('apAuthLocalUserMain', 'settingUpConnection'));
    $this->dispatcher->connect('authentication.request', array($this, 'registerAuthenticator'));
    $this->dispatcher->connect('authenticator.report_interface', array('apAuthLocalUserMain', 'getAuthenticatorTypes'));
    $this->dispatcher->connect('menu.build', array('apAuthLocalUserMain', 'getMenu'));
  }

  public static function isAuthPuppyPlugin() {
    return true;
  }

  public function registerAuthenticator() {
    apAuthentication::registerAuthenticator('apAuthLocalUser');
  }
}
