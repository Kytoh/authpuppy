<?php

/**
 * PluginapUser filter.
 *
 * @package    Authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    SVN: $Id: pre-alpha$
 */

abstract class PluginapUserFormFilter extends BaseapUserFormFilter
{
  public function configure() {
    unset($this->widgetSchema['password'], $this->widgetSchema['validation_token'], $this->widgetSchema['username_lower']);
    
    
    $this->widgetSchema['status'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apUser')->getStatuses(),
      'expanded' => false,
      'multiple' => true,  
    ));

    $statusvalidator = $this->validatorSchema['status'];
    $this->validatorSchema['status'] = 
      new sfValidatorChoice(array(
          'choices' => array_keys(Doctrine_Core::getTable('apUser')->getStatuses()),
          'multiple' => true,
          'required' => false,
    ));
  }
  
  /**
   * Adds the status query for multiple choices of status values
   * @param Doctrine_Query $query  The query to modify
   * @param string $field
   * @param array $values  The selected values
   */
  public function addStatusColumnQuery(Doctrine_Query $query, $field, $values) {
    if (!is_array($values)) 
      $values = array($values);
    $qwhere = array();
    foreach ($values as $val) {
      $qwhere[] = 'status = ?';
    }
    $swhere = implode(' OR ', $qwhere);
    $query->addWhere($swhere, $values);
  }
  
  /**
   * Adds the status query for multiple choices of status values
   * @param Doctrine_Query $query  The query to modify
   * @param string $field
   * @param array $values  The selected values
   */
  public function addUsernameColumnQuery(Doctrine_Query $query, $field, $values) {
    if (is_array($values)) 
      $values = array_shift($values);
    $query->addWhere('username_lower LIKE ?', '%'.strtolower($values).'%');
  }
}
