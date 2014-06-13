<?php

/**
 * apNodeExtraConfigureForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apNodeExtraConfigureForm extends apPluginManagerConfigurationForm
{
  protected $namespace = 'apNodeExtraConfigure';
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    $this->widgetSchema['allow_create_or_steal_nodes'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['allow_create_or_steal_nodes'] = new sfValidatorPass();
    $this->widgetSchema['map_centre_latitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['map_centre_latitude'] = new sfValidatorNumber(array('required' => false));
    $this->widgetSchema['map_centre_longitude'] = new sfWidgetFormInputText();
    $this->validatorSchema['map_centre_longitude'] = new sfValidatorNumber(array('required' => false));
    $this->widgetSchema['zoom_level'] = new sfWidgetFormInputText();
    $this->validatorSchema['zoom_level'] = new sfValidatorInteger(array('required' => false));
    
    // Get the list of available geocoders:
    $plugin_dir = sfConfig::get('sf_plugins_dir');
    $geocoder_dir = $plugin_dir. '/apNodeExtraPlugin/lib/model/Geocoders';
    //using the opendir function
    $geocoders_dir = @opendir($geocoder_dir);
    $geocoders = array();
    if ($geocoders_dir !== false) {
      //running the while loop
      while ($file = readdir($geocoders_dir)) 
      {
        // Each directory is a potential geocoder, we must get the name of the geocoding classes
        if (is_dir($geocoder_dir .'/'.$file) && ($file[0] != '.') ) {
          $adir = @opendir($geocoder_dir .'/'.$file);
          if ($adir !== false) {
            while ($indirfile= readdir($adir)) {
              if (is_file($geocoder_dir .'/'.$file . '/'.$indirfile)) {
                $classname = basename($indirfile, '.class.php');
                if (class_exists($classname)) {
                  $geocoders[$classname] = $file;
                  break;
                }  
              }
            }
          }
        }  
      }  
      //closing the directory
      closedir($geocoders_dir);
    }
    $this->widgetSchema['map_service'] = new sfWidgetFormChoice(array(
      'choices'  => $geocoders,
      'expanded' => false,
    ));
    
    $this->validatorSchema['map_service'] = new sfValidatorChoice(array(
      'choices' => array_keys($geocoders),
    ));
    
    $this->widgetSchema['cloudmade_key'] = new sfWidgetFormInputText();
    $this->validatorSchema['cloudmade_key'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['show_unmanaged_nodes_on_map'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1));
    $this->validatorSchema['show_unmanaged_nodes_on_map'] = new sfValidatorPass();
  }
  
  public function getPartial() {
    return "apNodeExtra/formConfigure";
  }
  
  public function findDefaults() {
    $this->setDefaults(array('allow_create_or_steal_nodes' => $this->plugin->getConfigValue('allow_create_or_steal_nodes', true),
                           'map_centre_latitude' => $this->plugin->getConfigValue('map_centre_latitude', 0),
                           'map_centre_longitude' => $this->plugin->getConfigValue('map_centre_longitude', 0),
                           'zoom_level' => $this->plugin->getConfigValue('zoom_level', 0),
                           'map_service' => $this->plugin->getConfigValue('map_service', 'apOpenLayersService'),
                           'cloudmade_key' => $this->plugin->getConfigValue('cloudmade_key', ''),
                           'show_unmanaged_nodes_on_map' => $this->plugin->getConfigValue('show_unmanaged_nodes_on_map', false),
                        ));
  }

}