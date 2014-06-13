<?php
/**
 * Web service V1 class
 *
 * mandatory parameters:
 * action: get|list
 *
 * Each action has its own set of parameters:
 *
 * get: get some information concerning a given object, identified by its id
 * 		parameters: object_class  The class of the object to get
 *               object_id  The id of the object
 *               fields  The list of fields to fetch (absent: all the allowed fields)
 *               id_type (o)  Not used yet
 *
 * list: get some informations concerning a list of objects
 *    parameters:  object_class The class of objects to list
 *               fields   The fields to list for each object
 *               parent_class (o)  The class of the parent object (for the nodes of a network, the class would be network)
 *               parent_id (o)  The id of the parent object
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @version    BZR: $Id$
 */

class apWS_V1 extends apWS
{
    /**
     * @var GET parameters of the function
     */
    protected $_action;
    protected $_objectClass;
    protected $_objectId;
    protected $_fields;

    /** @var list classes that are allowed to be fetched **/
    protected static $_allowedObjectClass = array('Node');
    /**
     * @var list allowed fields for each class
     * For each class, the array is such that the key is the field that will be requested in the GET request
     * the value is the name of the field in the app, such that class->getValue exists and returns
     */
    protected static $_allowedFields = array(
        'Node' => array('Name' => 'Name',
                        'GwId' => 'GwId',
                        'Description' => 'Description',
                        'CivicNumber' => 'CivicNumber',
                        'StreetName' => 'StreetName',
                        'City' => 'City',
                        'Province' => 'Province',
                        'Country' => 'Country',
                        'PostalCode' => 'PostalCode',
                        'Phone' => 'PublicPhoneNumber',
                        'Email' => 'PublicEmail',
                        'MassTransitInfo' => 'MassTransitInfo',
                        'Latitude' => 'Latitude',
                        'Longitude' => 'Longitude',
                        'Friendlyname' => 'Friendlyname',
                        'PortalPage' => 'PortalPage',
                        'NumOnlineUsers' => 'NumOnlineUsers',
                        'OnlineUsers' => 'OnlineUsers',
                        'CreationDate' => 'CreatedAt',
                        'Status' => 'DeploymentStatus'),
        'User' => array('Username' => 'Username',
                        'AccountOrigin' => 'AccountOrigin',
                        'Email' => 'Email' )
    );

    /**
      * Constructor for the web service
      *
      * */
    protected function __construct()
    {
        parent::__construct();
    }

	  /**
     * Set the web service parameters
     * @param $params   the arrray of GET parameters
     */
    public function setParams($params = array()) {

        if (isset($params['action'])) {
          $this->_action = $params['action'];
          unset($params['action']);
        }
        if (isset($params['object_class'])) {
          $this->_objectClass = $params['object_class'];
          unset($params['object_class']);
        }
        if (isset($params['object_id'])) {
          $this->_objectId = $params['object_id'];
          unset($params['object_id']);
        }
        if (isset($params['fields'])) {
          $this->_fields = $params['fields'];
          unset($params['fields']);
        }

        parent::setParams($params);
    }

    protected function mapFields($objectClass, $infields = array()) {
        $fields = array()   ;
        foreach($infields as $field) {
            if (isset(self::$_allowedFields[$objectClass][$field]))
                $fields[$field] = self::$_allowedFields[$objectClass][$field];
            else
                $fields[$field] = "$field.forbidden";
        }
        return $fields;
    }

    /**
     * This function executes the action requested by the web service
     * For the requested action, it verifies if the necessary parameters are there and then calls the appropriate function to really execute the function
     * @return unknown_type
     */
    protected function executeAction() {
        if (!isset($this->_action)) {
            throw new WSException("No action was specified.  Please use GET parameter 'action=list|get|auth' to specify an action", WSException::INVALID_PARAMETER);
        }
        switch($this->_action) {
            case 'list':
                $object_class = (isset($this->_objectClass) ? ucfirst(strtolower($this->_objectClass)): null);
                $fields = (isset($this->_fields) ? explode(',',$this->_fields): array());
                $parentClass = (isset($this->_params['parent_class']) ? $this->_params['parent_class']:null);
                $parentId = (isset($this->_params['parent_id']) ? $this->_params['parent_id']:null);
                $this->executeList($object_class, $fields, $parentClass, $parentId);
                break;
            case 'get':
                //$object_class = (isset($this->_objectClass) ? ucfirst(strtolower($this->_objectClass)): null);
                $object_class = $this->_objectClass;
                $object_id = (isset($this->_objectId) ? $this->_objectId: null);
                $fields = (isset($this->_fields) ? explode(',',$this->_fields): array());
                $idType = (isset($this->_params['id_type']) ? $this->_params['id_type']:null);
                $this->executeGet($object_class, $object_id, $fields, $idType);
                break;
            case 'auth':
                $authenticator = (isset($this->_params['authenticator']) ? $this->_params['authenticator']:null);
                $logout = (isset($this->_params['logout']) ? $this->_params['logout']:false);
                $this->executeAuth($authenticator, $logout);
                break;
            default:
                throw new WSException("Action {$this->_action} is not defined.  Please use GET parameter 'action=list|get|auth' to specify an action", WSException::INVALID_PARAMETER);
                break;
        }

    }

    /**
     * Verify the given user credentials against the authPuppy database
     * @param $authenticator  The authentification plugin to use
     * @param any             Any params needed for the auth plugin you use
     * @param $logout         Whether the user wants to logout
     * @return unknown_type
     */
    protected function executeAuth($authenticator, $logout = false) {
      $this->_outputArr['auth'] = 0;

      $node = null;
      if (!is_null($this->_request->getParameter('gw_id'))) {
        $node = Doctrine_Query::create()
                ->from('Node')
                ->where('lower(gw_id) = ?', strtolower($this->_request->getParameter('gw_id')))
                ->limit(1)
                ->fetchOne();
      }

      $token = null;
      if (!$logout) {
        /* TODO:  This is too much like the loginAction method and a change to that one needs to be done
         *        here too.  This should in a global authentication method instead
         */
        
        // Authenticate the user
        $dispatcher = sfProjectConfiguration::getActive()->getEventDispatcher();
        $dispatcher->notify(new sfEvent($this, 'authentication.request', array('node' => $node)));

        /* Provide access to authenticators */
        $this->authenticators = apAuthentication::getAuthenticators();

        /* Enable Splashonly on demand */
        // TODO: SECURITY, be able to know if is ok to use splashOnly for a specific node
        if ($authenticator == "slashOnly") {
          $this->authenticators['splashOnly'] = new apDefaultAuthenticator();
        }

        // foreach enabled authentication plugin, initiate them
        foreach ($this->authenticators as $authenticator) {
           $authenticator->setNode($node);
           $authenticator->initialize($this->_request, $this->_actions->getIdentity());
        }

        /**   If this request is a POST
         *      foreach enabled authentication plugin, process the form
         */
        // TODO: for release v1, enable only POST
        //if ($this->_request->isMethod('post')) {
          foreach ($this->authenticators as $authenticator) {
            $authenticator->process($this->_actions, $this->_request, $this->_actions->getIdentity());
          }
        //}

              // If the identity is set, create the connection and call the actionVx.class.php to do what is next
              if ($this->_actions->getIdentity()->isIdentified()) {
                $this->_outputArr['auth'] = 1;
                $dispatcher->notify(new sfEvent($this, 'authentication.success', array('identity' => $this->_actions->getIdentity())));
                $authmethod = $this->_actions->getIdentity()->getAuthenticatorType();

                // Add a privilege as logged in for the current user
                $this->_actions->getUser()->addCredential("Logged_" . $authmethod);
                $this->_actions->getUser()->addCredential("Logged");
                $this->_actions->getUser()->setAttribute('identity', $this->_actions->getIdentity());

                if ($node) {

                  /* Create connection */
                  $connection = new Connection();

                  $connection->setNodeRel($node);
                  $connection->setAuthType($authmethod);
                  $connection->setAuthSubType($this->_actions->getIdentity()->getAuthenticatorSubType());
                  $connection->setIdentity($this->_actions->getIdentity()->getId());

                  $event = $dispatcher->filter(new sfEvent($this->_actions, 'connection.pre_saved', array('connection' => $connection, 'identity' => $this->_actions->getIdentity())), $connection);
                  $connection = $event->getReturnValue();
                  $connection->save();
                  
                  /* Forward user straight back to router */
                  $this->_outputArr['redirect'] = 'http://' . $this->_request->getParameter('gw_address') . ':' . $this->_request->getParameter('gw_port') . '/wifidog/auth?token=' . $connection->getToken();
                }
              } else {
                // Authentification fail
                $this->_outputArr['auth'] = 0;
                $this->_outputArr = array_merge($this->_outputArr, $authenticator->getErrors());
              }



          } else {
            //logout
            if (!is_null($node)) {
              $token = $user->generateConnectionTokenNoSession($node, $from, $mac);
              if (!$token) throw new WSException("User authenticated but cannot generate connection token.", WSException::PROCESS_ERROR);
            }
          }
    }

    /**
     * Gets the requested fields from an object
     * @param $objectClass   The class of the object
     * @param $objectId      The id of the object to fetch
     * @param $fields        The list of fields to get
     * @return unknown_type
     */
    protected function executeGet($objectClass, $objectId, $fields = array(), $idtype = null) {
        if (is_null($objectClass)) {
            throw new WSException("Missing parameter 'object_class' in the request.", WSException::INVALID_PARAMETER);
        }
        if (is_null($objectId)) {
            throw new WSException("Missing parameter 'object_id' in the request.", WSException::INVALID_PARAMETER);
        }
        if (!in_array($objectClass,self::$_allowedObjectClass)) {
            throw new WSException("Wrong object class '{$objectClass}' requested.  Possible values are " . implode(', ', self::$_allowedObjectClass), WSException::INVALID_PARAMETER);
        }

        // Impossible to use this syntax because of php bug http://bugs.php.net/bug.php?id=31318.  But is valid though with php > 5.3, so using the ugly syntax below instead
        //$object = $objectClass::getObject($objectId);

        try {
            /*$q = Doctrine_Query::create()
              ->from($objectClass.' t')
              ->where('t.id = ?', $objectId);

            $object = $q->execute();*/
            $object = Doctrine_Core::getTable($objectClass)->findOneBy('GwId', $objectId);
        } catch (Exception $e) {
            $object = null;
        }

        // If the object still is not found, then return an error
        if (empty($object)) {
            throw new WSException("Object of class {$objectClass} with id {$objectId} not found", WSException::PROCESS_ERROR);
        }

        $fields = $this->mapFields($objectClass, $fields);
        if (empty($fields)) {
            $fields = self::$_allowedFields[$objectClass];
        }

        $this->_outputArr = self::filterRet($object, $fields);
    }

    /**
     * Get the list of all objectClass, for the given parent if specified or globally otherwise
     * @param $objectClass    The class whose object must be listed
     * @param $fields         The fields to list for each object
     * @param $parentClass    The parent class if necessary (for nodes for instance)
     * @param $parentId       The identifier of the parent object
     * @return unknown_type
     */
    protected function executeList($objectClass, $fields = array(), $parentClass = null, $parentId = null) {
        if (is_null($objectClass)) {
            throw new WSException("Missing parameter 'object_class' in the request.", WSException::INVALID_PARAMETER);
        }
        if (!in_array($objectClass,self::$_allowedObjectClass)) {
            throw new WSException("Wrong object class '{$objectClass}' requested.  Possible values are " . implode(', ', self::$_allowedObjectClass), WSException::INVALID_PARAMETER);
        }

        include_once('classes/'.$objectClass.'.php');

        $parentObject = null;
        if (!is_null($parentClass)) {
            if (!is_null($parentId)) {
                if (!in_array($parentClass,self::$_allowedObjectClass)) {
                    throw new WSException("Wrong parent class '{$parentClass}' specified.  Possible values are " . implode(', ', self::$_allowedObjectClass), WSException::INVALID_PARAMETER);
                }
                include_once('classes/'.$parentClass.'.php');
                $parentObject = call_user_func($parentClass.'::getObject', $parentId);
            } else {
                throw new WSException("If parent class is specified, must specify 'parent_id'", WSException::INVALID_PARAMETER);
            }
        }

        if (is_null($parentObject)) {
            if (method_exists($objectClass, 'getAll'.$objectClass.'s')) {
                $objectList = call_user_func($objectClass.'::getAll'.$objectClass.'s');
            }
        }
        $fields = $this->mapFields($objectClass, $fields);
        if (empty($fields)) {
            $fields = self::$_allowedFields[$objectClass];
        }

        if (!isset($objectList)) {
            throw new WSException("Object list for '{$objectClass}' is not supported.", WSException::GENERIC_EXCEPTION);
        }
        $this->_outputArr = self::filterRet($objectList, $fields);
    }

    /**
     * Filters the returned value to return only allowed fields if the returned value is an object
     * @param $retVals         array of mixed, array of objects or other arrays to filter
     * @param $fields          List of fields to filter (if none specified, for objects the allowed fields for the class is taken, otherwise, all is taken)
     * @return array | mixed
     */
    protected static function filterRet($retVals = array(), $fields = array()) {
        if (!is_array($retVals)) {
            $retVals = array($retVals);
        }
        $filtered = array();

        foreach($retVals as $key => $value) {
            // If the return is one object we filter, return only the allowed fields
            if (is_object($value)) {
                // Object class must be one of the allowed classes or else return nothing
                $object_class = get_class($value);
                if (in_array($object_class, self::$_allowedObjectClass)) {

                    // Get each allowed field
                    if (empty($fields)) {
                        $fields = self::$_allowedFields[$object_class];
                    }
                    $retFields = array();
                    foreach ($fields as $fkey => $field) {
                        $forbiddenfield = explode(".", $field);
                        if (! (count($forbiddenfield) == 2)) {
                            $methodName = 'get'.$field;
                            try {
                              $retFields[is_string($fkey)?$fkey:$field] = self::filterRet($value->$methodName());
                            } catch(Exception $e) {
                              $retFields[is_string($fkey)?$fkey:$field] = 'unknown';
                            }
                        } else
                            $retFields[$forbiddenfield[0]] = 'Not allowed';
                    }
                    $filtered[] = $retFields;
                }
                else {
                    $filtered[] = array();
                }
            } else if (is_array($value) && !empty($fields)) {
                $allowed_array = array();
                foreach ($fields as $field) {
                    if (isset($value[$field])) {
                        $allowed_array[$field] = $value[$field];
                    } else {
                        // In an array, the actual field name may be a _-separated string where the word separation is the uppercase
                        $fieldname = preg_replace("/([A-Z])/e", "'_'.strtolower('\\1')", $field);
                        // This preg_replace also put a _ before the first ucletter, which we don't want.
                        if ($fieldname[0] == '_')  $fieldname = substr($fieldname, 1);
                        if (isset($value[$fieldname])) {
                            $allowed_array[$field] = $value[$fieldname];
                        }
                        else {
                            $allowed_array[$field] = 'unknown2';
                        }
                    }
                }
                $filtered[] = $allowed_array;
            }
            else {
                $filtered[] = $value;
            }
        }
        return (count($filtered) == 1? $filtered[0]: $filtered);
    }

}
