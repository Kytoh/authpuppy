<?php

/**
 * PluginapUserTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginapUserTable extends Doctrine_Table
{
  CONST AUTHLOCALUSER_STATUS_DENIED =0;
  CONST AUTHLOCALUSER_STATUS_ALLOWED = 1;
  CONST AUTHLOCALUSER_STATUS_VALIDATION = 5;
  CONST AUTHLOCALUSER_STATUS_LOCKED = 127;

  static public $statuses = array(
    '0' => 'Denied',
    '1' => 'Allowed',
    '5' => 'Validation',
    '127' => 'Locked',
  );
 

    
    /**
     * Returns an instance of this class.
     *
     * @return object PluginapUserTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginapUser');
    }
    
    /**
    * Retrieves a apLogin object by username and is_active flag.
    *
    * @param  string  $username The username
    * @param  boolean $isActive The user's status
    *
    * @return apLogin
    */
    public function retrieveByUsername($username, $isActive = true)
    {
      $querystring = 'u.username = ?';
      if (!apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('username_case_sensitive', false)) {
        $querystring = 'u.username_lower = ?';
        $username = strtolower($username);
      }
      $query = Doctrine_Core::getTable('apUser')->createQuery('u')
        ->where($querystring, $username)
      ;
    
      return $query->fetchOne();
    }
    
    /**
    * Retrieves a apLogin object by username or email_address and is_active flag.
    *
    * @param  string  $username The username
    * @param  boolean $isActive The user's status
    *
    * @return apLogin
    */
    public function retrieveByUsernameOrEmailAddress($username, $isActive = true)
    {
      $querystring = 'u.username = ? OR u.email = ?';
      if (!apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('username_case_sensitive', false)) {
        $querystring = 'u.username_lower = ? OR u.email = ?';
        $username = strtolower($username);
      }
      $query = Doctrine_Core::getTable('apUser')->createQuery('u')
        ->where($querystring, array($username, strtolower($username)))
      ;
    
      return $query->fetchOne();
    }
    
  /**
   * Returns the query to get users
   * @return Doctrine_Query
   */
  public function getUsersQuery(Doctrine_Query $q = null)
  {
    if (is_null($q)) {
      $q = Doctrine_Query::create()
        ->from('apUser');
    }
 
    return $q;
  }
  
  public function getStatuses()
  {
    return self::$statuses;
  }
  
}