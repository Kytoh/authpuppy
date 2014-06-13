<?php

/**
 * PluginapLogin
 * 
 * A doctrine record to represent an identity
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */

abstract class PluginapUser extends BaseapUser
{
  protected $migrated = false;
  
  public function setUp() {
    parent::setUp();
    sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this,'localuser.set_up', array('user' => $this)),$this);
  }
  
  /**
   * Returns the string representation of the object: "Full Name (username)"
   *
   * @return string
   */
  public function __toString()
  {
    return $this->getUsername();
  }
  
  protected function hashPassword($password) {
     return base64_encode(pack("H*", md5(utf8_decode($password))));
  }

  /**
   * Sets the user password.
   *
   * @param string $password
   */
  public function setPassword($password)
  {
    if (!$password && 0 == strlen($password))
    {
      return;
    }

    $this->_set('password', $this->hashPassword($password));
  }

  /**
   * Returns whether or not the given password is valid.
   *
   * @param string $password
   * @return boolean
   */
  public function checkPassword($password)
  {
    return ($this->getPassword() == $this->hashPassword($password) || $this->getPassword() == $password);
  }
  
  /**
   * Generates an 8 character random password
   * @param $length integer, the length of the password
   * @return string
   */
  public function generateRandomPassword($length = 8, $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHJKLMNPQRTSUVWXYZ") {
    
    // start with a blank password
		$password = "";
		
		// set up a counter
		$i = 0; 
		    
		// add random characters to $password until $length is reached
		while ($i < $length) { 
		
		  // pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			        
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) { 
			  $password .= $char;
			  $i++;
			}
		
		}
		
		// done!
		return $password;
    
  }
  
  /**
   * Saves the new user and, if so configured, sends a validation email to new users
   * @param Doctrine_Connection $conn
   */
  public function save(Doctrine_Connection $conn = null)
  {
    // Fill the field username_lower
    $this->setUsernameLower(strtolower($this->getUsername()));
    if ($this->isNew() && !$this->migrated)
    {
      $now = $this->getRegisteredOn() ? $this->getDateTimeObject('registered_on')->format('U') : time();
      $this->setRegisteredOn(date('Y-m-d H:i:s', $now));
      if (apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('validate_by_email', true)) {
        $this->setValidationToken(apUtils::generateNonce());
        $this->setStatus(apUserTable::AUTHLOCALUSER_STATUS_VALIDATION);
        
        $message = new apMailMessage();
        $message->setFrom(array(apAuthpuppyConfig::getConfigOption("email_from", "from@noreply.com") => apAuthpuppyConfig::getConfigOption("name_from", "System Administrator")))
          ->setTo($this->getEmail())
          ->setSubject('Validation for '.$this->getUsername())
          ->setBodyFromPartial('apAuthLocalUserLogin/validate', array('login' => $this))
          ->setContentType('text/html')
        ;

        sfContext::getInstance()->getMailer()->send($message->getMailMessage());
        
      } else {
        $this->setStatus(apUserTable::AUTHLOCALUSER_STATUS_ALLOWED);
      }
    }
 
    return parent::save($conn);
  }
  
  public function getStatusText() {
    $statuses = Doctrine_Core::getTable('apUser')->getStatuses();
    return $statuses[$this->getStatus()];
  }
  
  public function setStatusLocked() {
    $this->setStatus(apUserTable::AUTHLOCALUSER_STATUS_LOCKED);
  }
  
  public function setStatusAllowed() {
    $this->setStatus(apUserTable::AUTHLOCALUSER_STATUS_ALLOWED);
  }
  
  public function setEmail($value)
  {
    $this->_set('email', strtolower($value));
  }
  
  public function setMigrated($value) {
    $this->migrated = $value;
  }
}