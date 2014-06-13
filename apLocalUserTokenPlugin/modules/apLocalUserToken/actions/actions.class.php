<?php

/**
 * apLocalUserToken actions.
 *
 * @package    authpuppy
 * @subpackage apLocalUserToken
 * @author     Frédéric Sheedy
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apLocalUserTokenActions extends sfActions
{
  
  /**
   * Get the filter array of values used to display the list
   */  
  protected function getFilter()
  {
    return $this->getUser()->getAttribute('apAuthLocalUser.filters', array());
  }

  /**
   * Sets the array of values used to display the list
   * @param unknown_type $filters
   */
  protected function setFilter(array $filters)
  {
    return $this->getUser()->setAttribute('apAuthLocalUser.filters',$filters);
  }
    
  
  protected function getUserNetworks() {
    // Get the network this user belongs to if any and build the query with it
    $user = $this->getUser()->getGuardUser();
    
    $networks = $user->SimpleNetworks->getPrimaryKeys();
    return $networks;
  }
  
  protected function userCanView($ap_physical_user) {
    $networks = $this->getUserNetworks();
    return empty($networks) || in_array($ap_physical_user->getSimpleNetworkId(), $networks);
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    // This is not a navigation, so the user came from somewhere else, we reset the filter
    if ($request->getParameter('page', 0) < 1) {
      $this->setFilter(array());
    }
    // Create the filter form 
    $this->filter = new apPhysicalUserFormFilter();
    if ($request->isMethod(sfRequest::POST)) {
      $this->filter->bind($request->getParameter($this->filter->getName()), $request->getFiles($this->filter->getName()));
      if ($this->filter->isValid())
      {   
        $this->setFilter($this->filter->getValues());
      }
    }
    // Build the query with the saved filters
    $query = $this->filter->buildQuery ($this->getFilter());
      
    // Add the networks for this query
    $networks = $this->getUserNetworks();
    if (count($networks) > 0) {
      $query->whereIn('simple_network_id', $networks);   
    }   
    
    // Initiates the pager for paging results
    $this->pager = new sfDoctrinePager(
      'apPhysicalUser',
      25
    );
    $this->pager->setQuery($query);
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new apPhysicalUserForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new apPhysicalUserForm();
    
    if ($this->processForm($request, $this->form)) {
      $this->mayCreateTicket($request, $this->form);
      $this->redirect('apLocalUserToken/edit?id='.$this->ap_physical_user->getId().'&has_ticket='.$this->hasTicket );
    }

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ap_physical_user = Doctrine::getTable('apPhysicalUser')->find(array($request->getParameter('id'))), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    $this->forward404Unless($this->userCanView($ap_physical_user), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    $this->has_ticket = 0;
    $this->has_ticket = $request->getParameter('has_ticket');
    $this->form = new apPhysicalUserForm($ap_physical_user);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ap_physical_user = Doctrine::getTable('apPhysicalUser')->find(array($request->getParameter('id'))), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    $this->forward404Unless($this->userCanView($ap_physical_user), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    $this->form = new apPhysicalUserForm($ap_physical_user);

    if ($this->processForm($request, $this->form)) {
      $this->mayCreateTicket($request, $this->form);
      $this->redirect('apLocalUserToken/edit?id='.$this->ap_physical_user->getId().'&has_ticket='.$this->hasTicket);
    }

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ap_physical_user = Doctrine::getTable('apPhysicalUser')->find(array($request->getParameter('id'))), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    $this->forward404Unless($this->userCanView($ap_physical_user), sprintf('Object ap_physical_user does not exist (%s).', $request->getParameter('id')));
    
    $ap_physical_user->delete();

    $this->redirect('apLocalUserToken/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $this->ap_physical_user = $form->save();

      return true;
    }
    return false;
  }
  
  public function executeViewTicket(sfWebRequest $request) {
    $ticket = $this->getUser()->getAttribute('ticket');
    $this->password = $ticket['password'];
    $this->apUser = $ticket['apUser'];
    $this->physicalUser = $this->apUser->apPhysicalUser;
    $this->network = $this->physicalUser->apSimpleNetwork;
    $this->profile = $this->apUser->apLocalUserProfiles;
  }
  
  protected function mayCreateTicket(sfWebRequest $request, sfForm $form) {
    $submit = $request->getParameter("submit");
    $this->hasTicket = 0;
    
    // If the submit button pressed was createticket, we need to create the ticket and show it to the user
    if (isset($submit['createticket'])) {
      $user = $this->ap_physical_user;
      $ticket = $user->createTicket($form->getValues());
      $this->getUser()->setAttribute('ticket', $ticket);
      $this->hasTicket = 1;
    }
  }
}
