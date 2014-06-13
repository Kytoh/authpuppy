<?php 

/**
 * apNodeListJson
 * 
 * json node list
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    BZR: $Id$
 */

class apNodeListJson extends NodeListAbstract  {
  

    /**
     * Sets header of output
     *
     * @return void
     */
  public function getOutput() {
    $arr = $this->generateList();  
    
    return json_encode($arr);
  }
}