<?php

/**
 * PluginapPhysicalUser form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginapPhysicalUserForm extends BaseapPhysicalUserForm
{
  public function setup() {
    parent::setup();
    unset($this['created_at']); unset($this['updated_at']);
    
    $years = range(date('Y') - 100, date('Y'));
    $this->widgetSchema['birth_date'] = new sfWidgetFormJQueryDate(array('date_widget' => new sfWidgetFormDate(array('years' => array_combine($years, $years)))));
    
    $this->validatorSchema['first_name']->addOption('trim', true);
    $this->validatorSchema['last_name']->addOption('trim', true);
    
    $this->widgetSchema['document_type'] = new sfWidgetFormChoice(array(
      'choices'  => Doctrine_Core::getTable('apPhysicalUser')->getDocumentTypes(),
      'expanded' => false,
    ));

    $this->validatorSchema['document_type'] = new sfValidatorChoice(array(
      'choices' => array_keys(Doctrine_Core::getTable('apPhysicalUser')->getDocumentTypes()),
    ));
    
    // If the actual user is linked to network, show only the node list for this network
    $user =  sfContext::getInstance()->getUser()->getGuardUser();

    $networks = array();
    if (is_object($user->SimpleNetworks)) {
      $networks = $user->SimpleNetworks->getPrimaryKeys();
    }
    if (count($networks) > 0) {
      $query = Doctrine_Core::getTable('apSimpleNetwork')->createQuery()
        ->whereIn('id', $networks); 
      $this->widgetSchema['simple_network_id']->setOption('query', $query);   
    } 
    $this->widgetSchema['simple_network_id']->setOption('add_empty', false);
    
    
    $localuserprofilesform = new apAddLocalUserProfilesForm(new apUser());
    $this->mergeForm($localuserprofilesform);
    
    $this->widgetSchema['local_user_profile_id']->setOption('add_empty', false);
    
    // Add some more ticket fields
    $payment_choices = array('Free' => 'Free', 'Cash' => 'Cash', 'Credit card' => 'Credit card',
                          'Debit card' => 'Debit card', 'Other' => 'Other');
    $this->widgetSchema['payment'] = new sfWidgetFormChoice(array(
      'choices'  => $payment_choices,
      'expanded' => false,
    ));

    $this->validatorSchema['payment'] = new sfValidatorChoice(array(
      'choices' => array_keys($payment_choices),
    ));
    
    $this->widgetSchema['ticket_notes'] = new sfWidgetFormTextarea(array(), array('rows' => 4));
    $this->validatorSchema['ticket_notes'] = new sfValidatorString(array('max_length' => 500, 'required' => false));
    
  }
}
