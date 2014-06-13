<?php
/**
 * Abstract class for the web service
 *
 * @todo Make all the setter functions no-op if the value is the same as what
 * was already stored Use setCustomPortalReduirectUrl as an example
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @version    BZR: $Id$
 */

abstract class apWS
{

    protected $_params;
    protected $_actions;
    protected $_request;

    /**
     * The appropriate output class
     * @var WSOutput
     */
    protected $_output;

    /**
     * Array of values to return
     */
    protected $_outputArr = array();

    /** Instantiate the web services.  The corresponding class is apWS_V$version
     * @param $version the version of the web service protocol
     * @return a WifidogWS object
     */
    public static function factory($version = 1)
    {
        if (!is_int($version))
            $version = 1;
        $classname = "apWS_V$version";
        
        if (!class_exists($classname, false)) {
            $classname="apWS_V1";
        }
        
        return (new $classname());
    }

     /**
      * Constructor for the web services
      *
      * */
    protected function __construct()
    {

    }

    /**
     * Returns the web services output class
     * @return WSOutput
     */
    public function getOutput() {
        return $this->_output;
    }

    /**
     * Returns the formatted output
     * @return string
     */
    public function output() {
        /*if (!isset($this->_output)) {
            throw new WSException("Can't output message because no output class is defined.");
        }
        return $this->outputSuccess($this->_outputArr);*/

      return $this->_outputArr;
    }

    /**
     * Set the web services parameters
     * @todo add class output verification
     * @param $params   the arrray of GET parameters
     */
    public function setParams($params = array()) {
        // Construct the output of the web services
        if (isset($params['f'])) {
            $this->_output = $params['f'];
            unset($params['f']);
        } else {
            $this->_output = "json";
        }
        $this->_params = $params;
    }

    public function setActions(sfAction $actions) {
      $this->_actions = $actions;
    }

    public function setRequest(sfWebRequest $request) {
      $this->_request = $request;
    }

    abstract protected function executeAction();

    /**
     * Passes the control to the web services class to execute the request
     * @return a properly formatted output
     */
    public function execute() {
        $this->executeAction();
    }
}
