<?php
/**
 * apGeocodeNode
 * 
 * This class geocodes a node
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

class apGeocodeNode {
    
  protected $values = null;
  protected $geocoder = null;
    
  public function __construct($values = array()) {
    $this->values = $values;
  }
  
  public function geocode(&$errmsg = '') {
    $geocoder = $this->getGeocoder();
    $values = $this->values;
    
    $geocoder->setCivicNumber($values['civic_number']);
    $geocoder->setStreetName($values['street_name']);
    $geocoder->setCity($values['city']);
    $geocoder->setProvince($values['province']);
    $geocoder->setCountry($values['country']);
    $geocoder->setPostalCode($values['postal_code']);
    
    $latitude = $geocoder->getLatitude();
    $longitude = $geocoder->getLongitude();
    $errmsg = $geocoder->getError();
    
    if ($latitude) {
      $this->values['latitude'] = $latitude;
    }
    if ($longitude) {
      $this->values['longitude'] = $longitude;
    }
    
    return ($latitude && $longitude);
    
  }
  
  public function getValues() {
    return $this->values;
  }
  
  protected function getGeocoder() {
    if (is_null($this->geocoder)) {
      $this->geocoder = apGeocoderAbstract::factory();
    } 
    return $this->geocoder;
  }
    
}