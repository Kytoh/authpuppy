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
 * apConnectionPoliciesActions
 * 
 * Implement the actions for the connection policies module
 * 
 * @package    apConnectionPoliciesPlugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.2$
 */

class apConnectionPoliciesActions extends apActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->policies = Doctrine::getTable('apConnectionPolicies')
      ->createQuery('a')
      ->execute(); 
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new apConnectionPoliciesForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new apConnectionPoliciesForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($policy = Doctrine::getTable('apConnectionPolicies')->find(array($request->getParameter('id'))), sprintf('Object policy does not exist (%s).', $request->getParameter('id')));
    $this->form = new apConnectionPoliciesForm($policy);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($policy = Doctrine::getTable('apConnectionPolicies')->find(array($request->getParameter('id'))), sprintf('Object policy does not exist (%s).', $request->getParameter('id')));
    $this->form = new apConnectionPoliciesForm($policy);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $policy = $form->save();

      $this->redirect('apConnectionPolicies/index');
    }
  }
  
  public function executeStatus(sfWebRequest $request) {
    // Use the current identity
    $identity = $this->getUser()->getAttribute('identity');
    
    if (!is_null($identity)) {
      $policies = Doctrine::getTable('apApplicablePolicies');
      $this->statuses = $policies->getForIdentity($identity);
    }
    else {
      $this->getUser()->setAttribute('needLogin', $this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());
      $this->forward('node', 'login');
    }
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($connection_policy = Doctrine::getTable('apConnectionPolicies')->find(array($request->getParameter('id'))), sprintf('Object ap_connection_policy does not exist (%s).', $request->getParameter('id')));
    $connection_policy->delete();

    $this->redirect('apConnectionPolicies/index');
  }
  
  
}