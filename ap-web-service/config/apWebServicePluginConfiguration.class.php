<?php

/**
 * Main configuration for the apWebService
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @version    Bzr: $Id: pre-alpha$
 */

class apWebServicePluginConfiguration extends sfPluginConfiguration {
 
  const version= "1.0.0-DEV";
  const plugin_name = "apWebServicesPlugin";

  public static function isAuthPuppyPlugin() {
    return true;
  }
}
