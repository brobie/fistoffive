<?php
class AbstractController extends Zend_Controller_Action
{	
	
	
	/**
	 * Add an error messages the the messages variable in the registry.
	 * @param string $message
	 */
    protected function addErrorMessage($message){
        $this->addMessage('error', $message);
    }
	
	/**
	 * Add an warning messages the the messages variable in the registry.
	 * @param string $message
	 */
    protected function addWarningMessage($message){
        $this->addMessage('warning', $message);
    }

    /**
	 * Add an info messages the the messages variable in the registry.
	 * @param string $message
	 */
    protected function addInfoMessage($message){
        $this->addMessage('info', $message);
    }
     
	/**
	 * Add a messages the the messages variable in the registry by type.
	 * @param string $type
	 * @param string $message
	 */
    protected function addMessage($type, $message){
    	$messageSession = new Zend_Session_Namespace('user_messages');
        $messages = $messageSession->messages;
        if ($messages == null){
            $messages = array();
            $messages['error'] = array();
            $messages['warning'] = array();
            $messages['info'] = array();
        }
        array_push($messages[$type], $message);
        $messageSession->messages = $messages;
    }
    
   
    
}