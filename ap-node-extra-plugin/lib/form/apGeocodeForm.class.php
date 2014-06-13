<?php

/**
 * apGeocodeForm
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
class apGeocodeForm extends BaseFormDoctrine
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    parent::setup();
    
    $this->widgetSchema['latitude'] = new sfWidgetFormInput();
    $this->widgetSchema['longitude'] = new sfWidgetFormInput();
    $this->widgetSchema->setHelp("latitude", '<input type="submit" name="submit[geocode]" value="Geocode"/>');
    
    $this->validatorSchema['latitude'] = new sfValidatorNumber(array('required' => false));
    $this->validatorSchema['longitude'] = new sfValidatorNumber(array('required' => false));
    
  }
  
  public function getModelName() {
      return 'Node'; 
  }

  public function geocode(sfEvent $event) {
    $params = $event->getParameters();
    $request = $params['request'];
    $submit = $request->getParameter("submit");
    
    // If the submit button pressed was geocode, geocode the node, otherwise pass on the event
    if (isset($submit['geocode'])) {
      $form = $params['form'];
      $values = $request->getParameter($form->getName());
      $nodegeocoder= new apGeocodeNode($values);
      if (!$nodegeocoder->geocode($errmsg)) {
        $event->getSubject()->getUser()->setFlash('error', $errmsg);
      } else {  
        $values = $nodegeocoder->getValues();
        $form->bind($values);
      }
      return true;
    } 
    return false;
      
  }

}