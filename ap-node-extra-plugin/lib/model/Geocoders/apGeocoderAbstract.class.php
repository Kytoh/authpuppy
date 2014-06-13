<?php

/**
 * apGeocoderAbstract
 * Part of this class has been ported from WifiDog
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    $Id: pre-alpha$
 */
abstract class apGeocoderAbstract 
{
  protected static $defaultGeocoder = 'apGoogleGeocodingV3WebService';  
  
  protected $civic_number = "";
  protected $street_name = "";
  protected $city = "";
  protected $province = "";
  protected $country = "";
  protected $postal_code = "";
  protected $result = null;
  
  protected $latitude = null;
  protected $longitude = null;
  protected $error = '';
  
  protected $execute = true;
  
  public static function factory() {
    $defaultGeocoder = apNodeExtraMain::getPlugin()->getConfigValue('geocoder', 'apGoogleGeocodingV3WebService');
    if (!class_exists($defaultGeocoder)) {
      $defaultGeocoder = self::$defaultGeocoder;
    }
    return new $defaultGeocoder();
  }
  
  public function getCivicNumber() {
    return $this->civic_number;
  }
  
  public function setCivicNumber($civic_number)
  {
    $this->trashResponse();
    $this->civic_number = $civic_number;
  }

  public function getStreetName()
  {
    return $this->street_name;
  }

  public function setStreetName($street_name)
  {
    $this->trashResponse();
    $this->street_name = $street_name;
  }

  public function getCity()
  {
    return $this->city;
  }

  public function setCity($city)
  {
    $this->trashResponse();
    $this->city = $city;
  }

  public function getProvince()
  {
    return $this->province;
  }

  public function setProvince($province)
  {
    $this->trashResponse();
    $this->province = $province;
  }

  public function getCountry()
  {
    return $this->country;
  }

  public function setCountry($country)
  {
    $this->trashResponse();
    $this->country = $country;
  }

  public function getPostalCode()
  {
    return $this->postal_code;
  }

  public function setPostalCode($postal_code)
  {
    $this->trashResponse();
    $this->postal_code = $postal_code;
  }
  
  public function getLatitude() {
    if (is_null($this->latitude)) {
      $this->processResult();
    }
    return $this->latitude;
  }
  
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  
  public function getLongitude() {
    if (is_null($this->longitude)) {
      $this->processResult();
    }
    return $this->longitude;
  }
  
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  
  public function getError() {
    return $this->error;
  }
  
  public function setError($err) {
    $this->error = $err;
  }
  
  /**
   * Gets the result of the query as returned by the geocoder, in raw format
   */
  public function getResult() {
    if ($this->shouldExecuteQuery()) {
      if ($this->validateAddress()) {
        $result = $this->executeQuery();
        $this->setResult($result);
      }
    }
    return $this->result;
  }
  
  public function setResult($result) {
    $this->execute_query = false;
    $this->result = $result;
  }
    
  protected function trashResponse()
  {
    $this->execute_query = true;
  }
  
  protected function shouldExecuteQuery()
  {
    return $this->execute_query;
  }
  
  abstract protected function validateAddress();
  abstract protected function executeQuery();
  abstract protected function processResult();
  abstract public function addJavascriptFiles(sfWebResponse &$response);
  
  abstract public function getJavascriptClass();

}