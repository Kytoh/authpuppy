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

class apNodeExtraActions extends apActions
{
  /**
   * This action presents the user a choice between creating a new node or
   *   stealing the node's id from another one.
   * If a new node should be created, the user is displayed the new node form, pre=filled with the gateway id
   * Otherwise, The node is fetched and his gateway id is changed for the one in parameter
   * @param $request
   */
  public function executeCreatesteal(sfWebRequest $request) {
    
    // create the form and find the default values from the get parameters
    $this->form = new apCreateStealForm();
    $this->form->findDefaults($request->getGetParameters());
    
    // If the request was a post, bind the parameters
    if ($request->isMethod(sfRequest::POST)) {
      $this->form->bind($request->getParameter("nodeextra"));
      if ($this->form->isValid()) {
        $submit = $request->getParameter('submit');
      
        // The user asked to create a new node, so we create the form with a pre-populated node
        // and display the new node template
        if (isset($submit['create'])) {
          $node = new Node();
          $node->setGwId($this->form->getValue('gw_id'));
          $this->form = new NodeForm($node);
          $this->setTemplate('new', 'node');
        } elseif (isset($submit['steal'])) {
          // If the node should be stolen, get the node to steal, change its gw_id and show the edit window
          $node = Doctrine::getTable('Node')->find($this->form->getValue('nodes_list'));
          $node->setGwId($this->form->getValue('gw_id'));
          $this->form = new NodeForm($node);
          $this->setTemplate('edit', 'node');
          $this->getUser()->setFlash('notice', "will steal the node");
        }
      }
    }
  }
  
/**
   * This action presents the user a choice between creating a new node or
   *   stealing the node's id from another one.
   * If a new node should be created, the user is displayed the new node form, pre=filled with the gateway id
   * Otherwise, The node is fetched and his gateway id is changed for the one in parameter
   * @param $request
   */
  public function executeNodelist(sfWebRequest $request) {
    $format = $request->getParameter('format');
    if (is_null($format)) $format = 'json';
    $class = 'apNodeList' . ucfirst(strtolower($format));
        
	// findout whether we should return unmanaged nodes
    $showunmanagednodes = apNodeExtraMain::getPlugin()->getConfigValue('show_unmanaged_nodes_on_map', false);
	//TODO: this somehow needs to set the onlyDeployed parameter to false if necessary?
    if (class_exists($class)) {
      $nodelist = new $class();
      $this->output = $nodelist->getOutput();
      if (method_exists($nodelist, 'setHeader')) {
          $this->getResponse()->clearHttpHeaders();
          $nodelist->setHeader($this->getResponse());
      }
      $this->setLayout(false);
    } else {
      $this->getUser()->setFlash('error', "The requested node format $format does not exist");
    }
    
  }
  
  public function executeMap(sfWebRequest $request) {
    // Get the class of the selected geocoder
    $geoclass = apNodeExtraMain::getPlugin()->getConfigValue('map_service', 'apOpenLayersService');
    $geoobj = new $geoclass();
    
    $response = $this->getResponse();
    
    $response->addMeta('viewport', 'initial-scale=1.0, user-scalable=no');
    $geoobj->addJavascriptFiles($response);
    $response->addStylesheet('/apNodeExtraPlugin/css/map.css');
    
    $this->latitude = apNodeExtraMain::getPlugin()->getConfigValue('map_centre_latitude', 0);
    $this->longitude = apNodeExtraMain::getPlugin()->getConfigValue('map_centre_longitude', 0);
    $this->zoom = apNodeExtraMain::getPlugin()->getConfigValue('zoom_level', 0);
    $this->key = apNodeExtraMain::getPlugin()->getConfigValue('cloudmade_key', false);
    $this->mapclass = $geoobj->getJavascriptClass();
    
  }
  
}