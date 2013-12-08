<?php
/**
 * This form is used to capture all of the data needed to register with the site.
 */
class Fof_Form_Register extends Zend_Form
{
    public function init()
    {
        $name = new Zend_Form_Element_Text('name');
		$name->setRequired(true);
		$name->addFilter('StringTrim');
		$name->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Please fill in your name.')));
        $name->setLabel('Your name:');
		$this->addElement($name);
        
		$emailValidator = new Zend_Validate_Regex("/[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/");
		$emailValidator->setMessages(array('regexNotMatch' => 'The email you entered is not valid.'));
		$email = new Zend_Form_Element_Text('email');
		$email->setRequired(true);
		$email->addFilter('StringTrim');
		$email->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Please fill in your email.')));
		$email->addValidator($emailValidator);
        $email->setLabel('Your email:');
		$this->addElement($email);
		
 		$password = new Zend_Form_Element_Password('password');
		$password->setRequired(true);
		$password->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Please fill in your password.')));
        $password->setLabel('Password:');
		$this->addElement($password);
		
		$confirmpassword = new Zend_Form_Element_Password('confirm_password');
		$confirmpassword->setRequired(true);
		$confirmpassword->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Please fill in your confirm password.')));
        $confirmpassword->setLabel('Confirm Password:');
		$this->addElement($confirmpassword);

		$register = $this->addElement('submit', 'submit', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Login',
        ));
    }
    
    public function getMessages(){
    	$messages = $this->_getErrorMessages();
    	$this->clearErrorMessages();
    	$elementMessages = parent::getMessages();
    	foreach ($messages as $index => $message){
    		$elementMessages['custom'.$index] = array('custom'=>$message);
    	}
    	return $elementMessages;
    }
    
    public function isValid($data){
    	
    	$isValid = parent::isValid($data);
    	if (!isset($data['tosagreement'])){
    		$this->addErrorMessage('You must agree to the terms of service to create an Examen.me account.');
    		$isValid = false;
    	}
    	if ($data['password'] != $data['confirm_password']){
    		$this->addErrorMessage('Your passwords do not match.');
    		$isValid = false;
    	}
    	if (strpos($data['email'], ' ') !== false){
    		$this->addErrorMessage('Your email is not valid.');
    		$isValid = false;
    	}
    	return $isValid;
    }
}