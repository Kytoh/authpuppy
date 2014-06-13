<?php

/**
 * PluginapSimpleNetwork form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginapSimpleNetworkForm extends BaseapSimpleNetworkForm
{
    public function setup() {
        parent::setup();
        $this->useFields(array('id', 'name', 'address', 'owner', 'email' ));
        
        $logoPicFileSrc = '/uploads/assets/apSimpleNetworksPlugin/'.$this->getObject()->getNetworkLogo();
        $this->widgetSchema['network_logo'] = new sfWidgetFormInputFileEditable(array('edit_mode' => true, 'with_delete' => false, 'file_src' => $logoPicFileSrc, 'is_image' => true), array('style' => 'max-width:400px;'));
        $this->validatorSchema['network_logo'] = new sfValidatorFile(array('path' => sfConfig::get('sf_upload_dir').'/assets/apSimpleNetworksPlugin', 'required' => false,'mime_types' => 'web_images'));
        $this->widgetSchema->setHelp('network_logo', 'The logo associated with the network');     
        
        $this->widgetSchema['users_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser'));
        $this->validatorSchema['users_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardUser', 'required' => false));
    }
    
  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['users_list']))
    {
      $this->setDefault('users_list', $this->object->Users->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveUsersList($con);

    parent::doSave($con);
  }

  public function saveUsersList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['users_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Users->getPrimaryKeys();
    $values = $this->getValue('users_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Users', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Users', array_values($link));
    }
  }
}
