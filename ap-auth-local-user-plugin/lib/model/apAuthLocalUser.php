<?php
/**
 * apAuthLocalUser
 * 
 * This class is the facade class of the authenticator, to be used by the application
 * 
 * 
 * @package    authpuppy
 * @subpackage plugin
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @author 		 Philippe April <philippe@philippeapril.com>
 * @version    BZR: $Id$
 */

class apAuthLocalUser extends apAuthentication {

  CONST SUB_TYPE_VALIDATION = "Validation";
  CONST SUB_TYPE_FORGOTPASSWORD = "Forgot password";
  CONST SUB_TYPE_AUTHENTICATED = "Authenticated";
  
  protected $_name = "Local network user authentication";
  protected $form;
  
  public function __construct() {
      $this->_name = apAuthLocalUserMain::getPlugin()->getConfigValue('authenticator_name', $this->_name);
  }

  /**
   * @see apAuthentication
   */
  public function initialize(sfWebRequest $request, apBaseIdentity $identity) {
    $params = $request->getParameter('link'); 
    if($params == 'forgotpwd') {
      $this->form = new apAuthLocalUserForgotPasswordForm();
    } elseif($params == 'signup') {
      $apUser = new apUser();
      $this->form = new apAuthLocalUserSignupForm($apUser);
    } else {
      $this->form = new apAuthLocalUserSigninForm(array(), array('node' => $this->getNode()));
    }
  }
  
  /**
   * @see apAuthentication
   */
  public function process(sfAction $action, sfWebRequest $request, apBaseIdentity $identity) {
    $params = $request->getParameter('submit');

    $signedin = false;
    if (isset($params['apAuthLocalUserconnect']) && ($this->form instanceof apAuthLocalUserSigninForm) ) {
      $this->form->bind($request->getParameter('apAuthLocalUser'));
      if ($this->form->isValid()) {
        $values = $this->form->getValues();  
        $login = $values['identity'];
        $this->form->rememberMe($values, $login); 
        
        $this->setSubType( ($login->getStatus() == apUserTable::AUTHLOCALUSER_STATUS_ALLOWED)?self::SUB_TYPE_AUTHENTICATED: self::SUB_TYPE_VALIDATION);
        sfProjectConfiguration::getActive()->getEventDispatcher()->filter(new sfEvent($this,'authlocaluser.settingSubType', array('user' => $values['identity'], 'authenticator' => $this)),$this);
        
        $identity->identify($values['identity']->getUsername(), $values['identity'], $this);
        $signedin = true;
      }
    } elseif (isset($params['apAuthLocalUserforgotpwd'])) {
      
      $this->form->bind($request->getParameter('apAuthLocalUserForgotpwd'));
      if ($this->form->isValid())
      {
        $values = $this->form->getValues(); 
        $login = $values['identity'];
        
        $new_pwd = $login->generateRandomPassword();
        $login->setPassword($new_pwd);
      
        $login->save();
        
        $this->form->newpwd = $new_pwd;

        $message = Swift_Message::newInstance()
          ->setFrom(array(apAuthpuppyConfig::getConfigOption("email_from", "from@noreply.com") => apAuthpuppyConfig::getConfigOption("name_from", "System Administrator")))
          ->setTo($login->getEmail())
          ->setSubject('Forgot Password Request for '.$login->getUsername())
          ->setBody($action->getPartial('apAuthLocalUserLogin/new_password', array('login' => $login, 'new_pwd' => $new_pwd)))
          ->setContentType('text/html')
        ;

        $action->getMailer()->send($message);
        $this->setSubType(self::SUB_TYPE_FORGOTPASSWORD);

        $identity->identify($login->getUsername(), $login, $this);
        $signedin = true;
      } 
      
    } elseif (isset($params['apAuthLocalUsersignup'])) {
      $params = $request->getParameter('ap_user');
      $params['username_lower'] = strtolower($params['username']);
      $this->form->bind($params);
      if ($this->form->isValid()) {
        // Save this new apUser and identify it
        $this->form->save();
        
        $apUser = $this->form->getObject();
        $this->setSubType(self::SUB_TYPE_VALIDATION);
        $this->form->rememberMe($apUser);
        $identity->identify($apUser->getUsername(), $apUser, $this);
        $signedin = true;
      }
    }
    
    // If user is signed in, verify if only one connection is allowed, if so, expire all previous connection for this user
    if ($signedin && apAuthLocalUserMain::getPlugin()->getConfigValue('one_connection_per_user', false)){
      $t = Doctrine::getTable("Connection");
      $q = $t->getQuery();
      $t->addIdentityQuery($q, $identity->getId());
      $t->addOnlineQuery($q);
      foreach ( $q->execute() as $conn) {
        $conn->setStatus(Connection::$EXPIRED);
        $conn->save();
      }
    }
    
  }
  
  
  
  
  /**
   * This function is called in the context of a view and returns the html string
   *   to render the authenticator
   * @return string
   */
  public function render() {
    return include_partial('apAuthLocalUserLogin/'.get_class($this->form), array('form' => $this->form));
  }
  
  public function getErrors() {
    if ($this->form->hasErrors()) {
      $errors = array();
      foreach ($this->form->getErrorSchema()->getErrors() as $k => $err) {
        $errors[$k] = $err->getMessage();
      }
      return $errors;
    } 
    return array();
  }
  
}
