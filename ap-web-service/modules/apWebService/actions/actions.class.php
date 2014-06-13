<?php

/**
 * apWebService
 *
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author     Frédéric Sheedy <sheedf@gmail.com>
 * @version    Bzr: $Id: pre-alpha$
 */
class apWebServiceActions extends apActions
{
  /**
   * Web service main
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $service = apWS::factory($request->getGetParameter("v"));
    $service->setParams($request->getGetParameters());
    $service->setActions($this);
    $service->setRequest($request);

    try{
      $service->execute();
    } catch (Exception $e) {
      $exceptionClass = get_class($e);

      if ($exceptionClass == 'WSException') {
        echo json_encode(array('result' => 0, 'values' => array('type' => $exceptionClass,
                               'message' => sprintf(_("Web service exception:  %s (%s)"),
                               $e->getMessage(), $e->getCode()))));
      } else {
        // todo
        echo "Error";
      }
      die();
    }

    $this->output = $service->output();

    print_r(json_encode(array('result' => 1, 'values' => $this->output)));
    die();
  }
}
