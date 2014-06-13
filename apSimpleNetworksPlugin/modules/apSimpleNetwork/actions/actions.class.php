<?php

/**
 * apSimpleNetwork actions.
 *
 * @package    authpuppy
 * @subpackage apSimpleNetwork
 * @author     Frédéric Sheedy
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apSimpleNetworkActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->ap_simple_networks = Doctrine::getTable('apSimpleNetwork')
      ->createQuery('a')
      ->execute();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new apSimpleNetworkForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new apSimpleNetworkForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($ap_simple_network = Doctrine::getTable('apSimpleNetwork')->find(array($request->getParameter('id'))), sprintf('Object ap_simple_network does not exist (%s).', $request->getParameter('id')));
    $this->form = new apSimpleNetworkForm($ap_simple_network);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ap_simple_network = Doctrine::getTable('apSimpleNetwork')->find(array($request->getParameter('id'))), sprintf('Object ap_simple_network does not exist (%s).', $request->getParameter('id')));
    $this->form = new apSimpleNetworkForm($ap_simple_network);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ap_simple_network = Doctrine::getTable('apSimpleNetwork')->find(array($request->getParameter('id'))), sprintf('Object ap_simple_network does not exist (%s).', $request->getParameter('id')));
    $ap_simple_network->delete();

    $this->redirect('apSimpleNetwork/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $ap_simple_network = $form->save();

      $this->redirect('apSimpleNetwork/edit?id='.$ap_simple_network->getId());
    }
  }
}
