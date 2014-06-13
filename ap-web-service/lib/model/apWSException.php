<?php
/**
 * Class for the web service exception
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @version    BZR: $Id$
 */

class WSException extends Exception
{
    CONST INVALID_PARAMETER = 8801;
    CONST GENERIC_EXCEPTION = 8800;
    CONST PROCESS_ERROR = 8802;

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 8800) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

}
