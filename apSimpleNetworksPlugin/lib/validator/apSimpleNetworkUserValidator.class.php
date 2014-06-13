<?php
/**
 * apSimpleNetworkUserValidator
 * 
 * Validate if the user entered is from the right network. the simple_network_id of both
 *   the apUser and the node need to be equal
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id$
 */

class apSimpleNetworkUserValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    if (isset($options['node'])) {
      $this->addOption('node', $options['node']);
    }
    $this->addOption('throw_global_error', true);

    $this->addMessage('wrongnetwork', 'You do not have access to this access point');
  }

  protected function doClean($values)
  { 
    $node = $this->getOption('node');
    $identity = $values['identity'];
    
    if (is_object($node)) {
      // Get the node network_id
      $node_network_id = $node->getSimpleNetworkId();
      $user_network_id = $identity->getSimpleNetworkId();
      
      // If both network ids are null, it is ok
      if (is_null($node_network_id) && is_null($user_network_id))
        return $values;
      elseif (is_null($node_network_id) || is_null($user_network_id))
        throw new sfValidatorError($this, 'wrongnetwork');
      elseif ($node_network_id != $user_network_id)
        throw new sfValidatorError($this, 'wrongnetwork');
      else return $values;
      
    } else {
      return $values;
    }
  }

  protected function getTable()
  {
    return Doctrine::getTable('apUser');
  }
}
