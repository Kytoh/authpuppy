<?php

/**
 * apAuthLocalUser plugin actions, for managing registrations and users.
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @version    Bzr: $Id: pre-alpha$
 */
class apAuthLocalUserLoginActions extends apActions
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
    
  
  public function executeIndex(sfWebRequest $request)
  {
    // This is not a navigation, so the user came from somewhere else, we reset the filter
    if ($request->getParameter('page', 0) < 1) {
      $this->setFilter(array());
    }
    // Create the filter form 
    $this->filter = new apUserFormFilter();
    if ($request->isMethod(sfRequest::POST)) {
      $this->filter->bind($request->getParameter($this->filter->getName()), $request->getFiles($this->filter->getName()));
      if ($this->filter->isValid())
      {   
        $this->setFilter($this->filter->getValues());
      }
    }
    // Build the query with the saved filters
    $query = $this->filter->buildQuery ($this->getFilter());   
    
    // Initiates the pager for paging results
    $this->pager = new sfDoctrinePager(
      'apUser',
      apAuthLocalUserMain::getPlugin()->getConfigValue('list_paging', 25)
    );
    $this->pager->setQuery(Doctrine::getTable('apUser')->getUsersQuery($query));
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
      
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->login = $this->getRoute()->getObject();
    $this->forward404Unless($this->login);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new apUserForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new apUserForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->form = new apUserForm($this->getRoute()->getObject());
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($login = Doctrine::getTable('apUser')->find(array($request->getParameter('id'))), sprintf('Object user does not exist (%s).', $request->getParameter('id')));
    $this->form = new apUserForm($login);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
 
    $login = $this->getRoute()->getObject();
    
    if (apPlugin::getPlugin('apAuthLocalUserPlugin')->getConfigValue('allow_delete_users', false)) {
      $login->delete();
    }
    else {
      $login->setStatusLocked();
      $login->save();
      $this->getUser()->setFlash("notice", "Users has not been deleted because it is forbidden by the application but his status has been set to 'locked'");
    }
   
 
    $this->redirect('ap_authlocaluser_login');
  }
  
  public function executeValidate(sfWebRequest $request)
  {
    $this->login = $this->getRoute()->getObject();
    $this->forward404Unless($this->login);
    
    $this->login->setStatusAllowed();
    $this->login->save();
    
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The user was created successfully.' : 'The user was updated successfully.';
      $login = $form->save();

      $this->getUser()->setFlash('notice', $notice);
      $this->redirect('ap_authlocaluser_edit', $login);
    }
  }
  
  public function executeMyaccount(sfWebRequest $request)
  {
    // Use the current identity
    $identity = $this->getUser()->getAttribute('identity');
    
    if (!is_null($identity) && ($identity->getAuthenticator() instanceof apAuthLocalUser)) {
      $this->form = new apAuthLocalUserMyAccountForm($identity->getIdentityObject());
      
      if ($request->isMethod(sfRequest::POST)) {
        $this->form->bind($request->getParameter("ap_user"));
        if ($this->form->isValid()) {
          $this->form->save();
          $this->getUser()->setFlash('notice', "Successfully saved.");
        } 
      }
    }
    else {
      $this->getUser()->setAttribute('needLogin', $this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());
      $this->forward('node', 'login');
    }
  }

  
}