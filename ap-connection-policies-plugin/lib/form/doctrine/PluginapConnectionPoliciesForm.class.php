<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | =============================                                          |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+


/**
 * PluginConnectionPolicies form.
 *
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */
abstract class PluginapConnectionPoliciesForm extends BaseapConnectionPoliciesForm
{
  public function setup()
  {
    parent::setup();
    // TODO: for now, most of these are not supported, so left out
    unset($this['max_incoming'], $this['max_outgoing']);
    
    $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apConnectionPolicies')->getTypes(),
      'expanded' => false,
    ));
    
    $this->widgetSchema['status_message'] = new sfWidgetFormTextarea(array('label' => 'Expiry Message'));
    
    $this->validatorSchema['type'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('apConnectionPolicies')->getTypes()),
    ));
    
    $this->widgetSchema['scope'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apConnectionPolicies')->getScopes(),
      'expanded' => false,
    ));
    
    $this->validatorSchema['scope'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('apConnectionPolicies')->getScopes()),
    ));
    
    $this->widgetSchema['auth_type'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apConnectionPolicies')->getAuthTypes(),
      'expanded' => false,
    ));
    
    $this->validatorSchema['auth_type'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('apConnectionPolicies')->getAuthTypes()),
    ));
    
    $this->widgetSchema['auth_sub_type'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apConnectionPolicies')->getAuthSubTypes(),
      'expanded' => false,
    ));
    
    $this->validatorSchema['auth_sub_type'] = new sfValidatorPass();
    
    $this->widgetSchema['max_total']->setLabel("Max total bytes");
    $this->widgetSchema->setHelp('max_total', "Total number of bytes (upload and download) this identity is allowed to transfer");
    $this->widgetSchema->setHelp('max_duration', "Maximum amount of time one can be actively connected.  Expected format: [0-9]+ (minute|hour|day|week|month)");
    $this->widgetSchema->setHelp('expiration', "Absolute amount of time from the first connection before the identity expires.  Expected format: [0-9]+ (minute|hour|day|week|month)");
    $this->widgetSchema->setHelp('time_window', "If type is relative, this value is used as the time window back from which the transfer or duration is calculated.  Expected format: [0-9]+ (minute|hour|day|week|month)");
    $this->widgetSchema->setHelp('type', "Absolute: duration or transfer total apply for the life of the identity; Relative: duration or transfer total apply to the last time window");
    $this->widgetSchema->setHelp('scope', "Global: for all nodes; Local: only on the current node");
    $this->widgetSchema->setHelp('identity_and_machine', "If checked, the policies is valid not only for the identity connecting, but also for the machine (MAC address)");
    
 
    $this->validatorSchema['time_window'] = new sfValidatorRegex(array('pattern' => "/^[0-9]+( minute| hour| day| week| month)?$/"));
    $this->validatorSchema['time_window']->setOption('required', false);
    
    $this->validatorSchema['max_duration'] = new sfValidatorRegex(array('pattern' => "/^[0-9]+( minute| hour| day| week| month)?$/"));
    $this->validatorSchema['max_duration']->setOption('required', false);
    
    $this->validatorSchema['expiration'] = new sfValidatorRegex(array('pattern' => "/^[0-9]+( minute| hour| day| week| month)?$/"));
    $this->validatorSchema['expiration']->setOption('required', false);
    $this->widgetSchema->moveField('expiration', 'after', 'max_duration');
    
    // TODO: The default postvalidator also has the auth_sub_type, so this line should be removed when the auth_sub_type is added to the form
    $this->validatorSchema->getPostValidator()->setMessage('invalid', 'A policy with identical values for "%column%" already exists.');
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'subTypeInType')),  array('invalid' => 'The sub auth type selected must be in the selected auth type')));
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'relativeAndTimeWindow')),  array('invalid' => 'If the type is specified as relative, then you must specify a time window')));
    
  }
  
  public function setDefaults($defaults) {
    if (isset($defaults['time_window']))
      $defaults['time_window'] = $this->object->stringFromInterval($defaults['time_window']);
    if (isset($defaults['max_duration']))
      $defaults['max_duration'] = $this->object->stringFromInterval($defaults['max_duration']);
    if (isset($defaults['expiration']))
      $defaults['expiration'] = $this->object->stringFromInterval($defaults['expiration']);
    parent::setDefaults($defaults);
    
  }
  
  protected function updateTimeWindow($value) {
    return $this->object->intervalStringFromValue($value);
  }
  
  protected function updateMaxDuration($value) {
    return $this->object->intervalStringFromValue($value);
  }
  
  protected function updateExpiration($value) {
    return $this->object->intervalStringFromValue($value);
  }
  
  public function subTypeInType($validator, $values) {
    if (!Doctrine_Core::getTable('apConnectionPolicies')->subTypeInType($values['auth_sub_type'], $values['auth_type'])) {
      throw new sfValidatorError($validator, 'invalid');
    }   
    return $values;
  }
  
  public function relativeAndTimeWindow($validator, $values) {
    if ($values['type'] == apConnectionPoliciesTable::TYPE_RELATIVE) {
      if ($values['time_window'] == '')
        throw new sfValidatorError($validator, 'invalid');
    }   
    return $values;
  }
}
