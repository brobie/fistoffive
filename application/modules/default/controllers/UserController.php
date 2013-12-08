<?php

require_once 'AbstractController.php';

class UserController extends AbstractController
{


	/**
	 * This function prepares the login page.  Many actions render the login page, so this function is hear to set up the title
	 * of the page.
	 */
	protected function _prepLoginView() {
		$this->view->title = "Free Online Devotional &amp; Quiet Time Tool - Pray, Meditate on Scripture, and Journal - Prayer of Examen, Lectio Divina | EXAMEN.me";
	}

	/**
	 * This function prepares the register page.  Many actions render the register page, so this function is hear to set up the title
	 * of the page.
	 */
	protected function _prepRegisterView() {
		$this->view->title = "Free Online Devotional &amp; Quiet Time Tool - Pray, Meditate on Scripture, and Journal - Prayer of Examen, Lectio Divina | EXAMEN.me";
	}

	/**
	 * Gets the form used to login to the site.
	 */
	protected function getLoginForm()
	{
		return new Fof_Form_Login(array(
            'action' => '/user/authenticate',
            'method' => 'post',
		));
	}

	/**
	 * Gets the form used to register with the site.
	 */
	protected function getRegisterForm()
	{
		return new Fof_Form_Register(array(
            'action' => '/user/create',
            'method' => 'post',
		));
	}


	
	/**
	 * This action is what the login form posts to.  This action does all of the work to see if the data entered on the form matches
	 * a user in the user table.  If they match, log them in, if not display an error to the user.
	 */
	public function authenticateAction()
	{

		Fof_Permission::resetSession();
			
		$request = $this->getRequest();

		// Check if we have a POST request
		if (!$request->isPost()) {
			return $this->_helper->redirector('login');
		}

		Fof_Model_Util::log('Trying to login with: ['.$request->getParam('email').'] and teh password of: ['. $request->getParam('password') .']');
		
		
		// Get our form and validate it
		$form = $this->getLoginForm();
		if (!$this->validatePostedForm($form)) {
			$this->_prepLoginView();
			return $this->render('login'); // re-render the login form
		}

		
		
		// Get our authentication adapter and check credentials
		$user = new Fof_Model_User();
		$result = $user->authenticate($form->getValues());
		if (!$result) {
			// Invalid credentials
			$this->view->form = $form;
			$this->_prepLoginView();
			$this->view->params = $request->getPost();
			$this->addErrorMessage('You have entered an invalid username or password.  Please Try Again.');
			Fof_Model_Util::log('Login for: ['.$request->getParam('email').'] failed.');
			return $this->render('login'); // re-render the login form
		}
		
		if ($result->active_switch != 'Y'){
			$this->view->form = $form;
			$this->_prepLoginView();
			$this->view->params = $request->getPost();
			$this->addErrorMessage('You are not active yet.');
			Zend_Auth::getInstance()->clearIdentity();
			Fof_Model_Util::log('Login for: ['.$request->getParam('email').'] failed (inactive).');
			return $this->render('login');
		}
		
		Zend_Auth::getInstance()->getStorage()->write($result);
		$authNamespace = new Zend_Session_Namespace('Zend_Auth');
		$authNamespace->setExpirationSeconds(86400);
		Fof_Model_Util::logUserAction('Logged In');
		Fof_Model_Util::log('Login for: ['.$request->getParam('email').'] succeeded!');
		// We're authenticated! Redirect to the home page
		if ($redirectUri = $this->getRedirectFromSession(true)){
			$this->_redirect($redirectUri);
		} else if ($redirectUri = $this->getGoToAfterLogin()){
			$this->_redirect($redirectUri);
		} else {
			$this->_helper->redirector('list', 'examen');
		}

	}

	/**
	 * This action creates a new user for the site.  If the new user is created, a call is made to campaign monitor to add
	 * the new user to the email list.
	 */
	public function createAction(){
			
		Fof_Permission::resetSession();
			
		$request = $this->getRequest();

		// Check if we have a POST request
		if (!$request->isPost()) {
			return $this->_helper->redirector('register');
		}

		// Get our form and validate it
		$form = $this->getRegisterForm();
		if (!$this->validatePostedForm($form)) {
			return $this->render('register'); // re-render the login form
		}
			
		$user = new Fof_Model_User();
		$params = $request->getPost();
		$errors = $user->create($params);
		if (count($errors) > 0){
			$this->view->form = $this->getRegisterForm();
			$this->_prepRegisterView();
			$this->view->params = $params;

			$this->addErrorMessage($errors[0]);

			return $this->render('register'); // re-render the register form
		}
			
		// We're authenticated! Redirect to the home page
		// Get our authentication adapter and check credentials
		$result = $user->authenticate($form->getValues());
		if (!$result) {
			// Invalid credentials
			$this->view->form = $form;
			$this->_prepLoginView();
			$this->view->params = $request->getPost();
			$this->addErrorMessage('There was something wrong with your registration, please contact technical support.');
			return $this->render('login'); // re-render the login form
		}
		Zend_Auth::getInstance()->getStorage()->write($result);
		Fof_Model_Util::logUserAction('Registered');

		// We're authenticated! Redirect to the home page
		if ($redirectUri = $this->getRedirectFromSession(true)){
			$this->_redirect($redirectUri);
		} else {
			$this->_helper->redirector('done-registering');
		}


	}

	/**
	 * This action logs the user out from the site.
	 */
	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		Fof_Model_User_Session::getInstance()->invalidate();
		Fof_Model_Util::logUserAction("Logged Out");
		$this->_helper->redirector('index', 'index'); // back to login page
	}

	/**
	 * This action shows the login page.
	 * The action used to log the user in is authenticateAction()
	 */
	public function loginAction()
	{
		$this->_prepLoginView();
	}

	/**
	 * This action shows the registration page.
	 * The action used to actual register the user is createAction()
	 */
	public function registerAction()
	{
		$this->_prepRegisterView();
		if($this->getRequest()->isPost()){
			$this->view->params = $this->getRequest()->getPost();
		}
	}

	/**
	 * This action shows the confirmation page after a user registers.
	 */
	public function doneRegisteringAction()
	{
		$this->view->title = "EXAMEN.me | Welcome";
	}

	/**
	 * This action shows the user profile page where a user can change things about themselves.
	 */
	public function profileAction()
	{
		
		if (!Zend_Auth::getInstance()->hasIdentity()){
			$this->addErrorMessage('Only logged in users can access this page.  If you were logged in, your session might have expired. Please log in and try again.');
			$session = new Zend_Session_Namespace('logged_in_go_to_session');
			$session->goto = $_SERVER['REQUEST_URI'];
			return $this->_redirect('/');
		}
		
		$this->view->title = "EXAMEN.me | My Profile";
		$this->view->navigationPhtml = 'navigation-myexamen.phtml';

		$user = new Fof_Model_User();
		$user->find(Zend_Auth::getInstance()->getStorage()->read()->user_id);
		$this->view->userName = $user->getName();
		$this->view->userEmail = $user->getEmail();
		$this->view->userShowVerseNumber = $user->getShowVerseNumbers();
		$this->view->userEmailReminder = $user->getEmailReminder();
		$this->view->userEmailPermission = $user->getEmailPermission();
	}

	/**
	 * This action shows the unsubscribe page where a user can unsubscribe from emails without logging in.
	 */
	public function unsubscribeAction()
	{
		$this->view->title = "EXAMEN.me | Unsubscribe";
		$e = $this->getRequest()->getParam('e');
		if ($e){
			$params = array('email'=>base64_decode($e));
			$this->view->params = $params;
		}
	}

	/**
	 * This action actually does the unsubscribe
	 */
	public function doUnsubscribeAction()
	{
		$this->view->title = "EXAMEN.me | Unsubscribe";
		if($this->getRequest()->isPost()){
			$email = $this->getRequest()->getParam('email');
			$user = new Fof_Model_User();
			if ($email){
				$user->findByEmail($email);
				if ($user->getUserId()){
					$user->setEmailPermission('N');
					Fof_Model_Util::removeUserFromCampaignList($email);
					$user->save();
					$this->addInfoMessage('You have been unsubscribed.');
				} else {
					$this->view->params = $this->getRequest()->getParams();
					$this->addErrorMessage('The email you entered could not be found.');
				}
			} else {
				$this->view->params = $this->getRequest()->getParams();
				$this->addErrorMessage('Enter an email.');
			}
			return $this->render('unsubscribe');
		}
			
		//$this->view->navigationPhtml = 'navigation-myexamen.phtml';
	}

	/**
	 * This action changes information about the user.  This could include their name, email, password, etc...
	 */
	public function doSaveProfileAction(){

		if (!$this->getRequest()->isPost()) {
			return $this->_helper->redirector('login');
		}
		$this->view->title = "EXAMEN.me | My Profile";
		$this->view->navigationPhtml = 'navigation-myexamen.phtml';
		$params = $this->getRequest()->getPost();
		$userdata = Zend_Auth::getInstance()->getStorage()->read();

		$this->view->userName = ($params['change_name'] ? $params['change_name'] : $userdata->name);
		$this->view->userEmail = ($params['change_email'] ? $params['change_email'] : $userdata->email);
		$this->view->userShowVerseNumber = ($params['change_show_verse_numbers'] ? $params['change_show_verse_numbers'] : $userdata->show_verse_numbers);
		$this->view->userEmailPermission = ($params['change_permission'] ? $params['change_permission'] : $userdata->email_permission);
		$this->view->userEmailReminder = ($params['change_email_reminder'] ? $params['change_email_reminder'] : $userdata->email_reminder);

		if ($params['newpassword'] != $params['confirmpassword']) {
			$this->addErrorMessage('Passwords do not match.');
			return $this->render('profile');
		}

		$user = new Fof_Model_User();
		$user = $user->find($userdata->user_id);
		if ($params['newpassword']){
			$user->setPassword(Fof_Model_Util::encodeEncrypt($params['newpassword']));
		}
		if ($params['change_name']){
			$user->setName($params['change_name']);
		}
		if ($params['change_email']){
			$emailValidator = new Zend_Validate_Regex("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/");
			if(!$emailValidator->isValid($params['change_email'])){
				$this->addErrorMessage('The email address is not valid.');
				return $this->render('profile');
			} else {
				$previousEmail = $user->getEmail();
				$user->setEmail($params['change_email']);
				if (!$user->isUnique() && $previousEmail != $user->getEmail()) {
					$this->addErrorMessage('An account with this email address already exists.  Please choose another.');
					return $this->render('profile');
				}
			}
		}
		if ($params['change_permission']){
			$user->setEmailPermission($params['change_permission']);
		}
		if ($params['change_email_reminder']){
			$user->setEmailReminder($params['change_email_reminder']);
		}
		if ($params['change_show_verse_numbers']){
			$user->setShowVerseNumbers($params['change_show_verse_numbers']);
		}

		$user->save();

		Zend_Auth::getInstance()->getStorage()->read()->name = $user->getName();
		Zend_Auth::getInstance()->getStorage()->write(
		Zend_Auth::getInstance()->getStorage()->read()
		);


		Fof_Model_Util::logUserAction('Changed Settings');

		$this->addInfoMessage('Your settings have been saved.');
		return $this->render('profile');

			
	}

	/**
	 * This action removes the user from the system.
	 */
	public function doCancelAction()
	{
		$userdata = Zend_Auth::getInstance()->getStorage()->read();
		$user = new Fof_Model_User();
		$user->setUserId($userdata->user_id);
		$user->delete();
		$this->_helper->redirector('logout', 'user');
	}
}
