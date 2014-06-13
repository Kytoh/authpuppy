<?php

/**
 * PluginapPhysicalUser form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginapPhysicalUserFormFilter extends BaseapPhysicalUserFormFilter
{
  public function setup() {
    parent::setup();
    $this->useFields(array('first_name', 'last_name', 'document', 'created_at'));
    $this->widgetSchema['document']->setOption('with_empty', false);
  }
}
