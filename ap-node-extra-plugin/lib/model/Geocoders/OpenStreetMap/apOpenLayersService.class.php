<?php
/**
 * apOpenLayersService
 * 
 * OpenLayers mapping class.  Uses the Google API for geocoding an address, but openlayers
 *   files to display the map
 *   
 * TODO Change geocoding functionnalities for GPL ones
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

class apOpenLayersService extends apGeocoderAbstract {
    
  protected $url = "http://maps.google.com/maps/api/geocode/";
  
  /**
   * Builds the address parameter for the query
   */
  protected function buildAddress() {
    // Build correctly formated string depending on given parameters
    $address = '';

    $addrarr = array();
    if ($this->getCivicNumber() != '') 
      $address .= $this->getCivicNumber();
    if ($this->getStreetName() != "")
      $address .= (($address != '')?" ":"") .$this->getStreetName();
    if ($address != '') $addrarr[] = $address;

    if ($this->getCity() != "")
      $addrarr[] = $this->getCity();

    if ($this->getProvince() != "")
      $addrarr[] = $this->getProvince();

    //$addrarr[] = $this->getPostalCode();
    return implode(", ", $addrarr);
  }
  
  protected function buildQuery($output = 'xml') {
    $http_params = array ("address" => $this->buildAddress(),"sensor" => "false");
    return $this->url . $output .'?' . http_build_query($http_params); // http://maps.google.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false
  }
    
  protected function executeQuery() {
     
    if ($this->shouldExecuteQuery()) {
     // Load the XML document
     // return file_get_contents($this->buildQuery());
      $url = $this->buildQuery();
      $xmlfile = apUtils::fetchUrl($url);

      return $xmlfile;
    } else {
      return null;
    }
    
  }
  
  protected function validateAddress(){
    return true;
  }
  
  protected function processResult() {
    $result = $this->getResult();
    if ($result) {
      $xml = new SimpleXMLElement($result);
      
      $status = (string) $xml->status;
      if (strcmp($status, "OK") == 0) 
      {
        // Successful geocode
        $coordinates = $xml->result->geometry->location;
                    
        // Format: Longitude, Latitude, Altitude
        $this->setLatitude((float)$coordinates->lat);
        $this->setLongitude((float) $coordinates->lng);
    
      } else {
        switch ($status) {
          case "ZERO_RESULTS" : $this->setError("Geocoding the address: no result found.");
            break;
          case "OVER_QUERY": $this->setError("Geocoding the address: you surpassed your limit of queries");
            break;
          case "REQUEST_DENIED": $this->setError("Geocoding the address: request denied.");
            break;
          case "INVALID_REQUEST": $this->setError("Geocoding the address: invalid request.  Missing parameter?");
            break;
        }
      }
    } else {
      $this->setLatitude(false);
      $this->setLongitude(false);
      $this->setError("Could not get the result from the Google Map API.");
    }
  }
  
  public function addJavascriptFiles(sfWebResponse &$response) {
    $response->addJavascript('/apNodeExtraPlugin/js/hotspots_map_osm.js');
  }
  
  public function getJavascriptClass() {
    return "OsmHotspotMap";
  }
  
    
}