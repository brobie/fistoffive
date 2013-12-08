<?php

require_once 'Zend/View/Helper/Abstract.php';

class Fof_Helper_Messages extends Zend_View_Helper_Abstract
{
    public function messages($type = 'all', $class = true){
		$messageSession = new Zend_Session_Namespace('user_messages');
        $allmessages = $messageSession->messages;
        $messages = '';
        if ($type == 'all'){
            if (isset($allmessages['error'])){
                foreach ($allmessages['error'] as $message){
                    $messages .= '<div '. ($class ? 'class="alert"': '') .'>' . $message . '</div>';
                }
                $allmessages['error'] = array();
            }
            if (isset($allmessages['warning'])){
                foreach ($allmessages['warning'] as $message){
                   $messages .= '<div '. ($class ? 'class="alert-warn"': '') .'>' . $message . '</div>';
                }
                $allmessages['warning'] = array();
            }
            if (isset($allmessages['info'])){
                foreach ($allmessages['info'] as $message){
                   $messages .= '<div '. ($class ? 'class="alert-pos"': '') .'>' . $message . '</div>';
                }
                $allmessages['info'] = array();
            }
        } else if ($type == 'error'){
            if (isset($allmessages['error'])){
                foreach ($allmessages['error'] as $message){
                   $messages .= '<div '. ($class ? 'class="alert"': '') .'>' . $message . '</div>';
                }
                $allmessages['error'] = array();
            }
        } else if ($type == 'warning'){
            if (isset($allmessages['warning'])){
                foreach ($allmessages['warning'] as $message){
                   $messages .= '<div '. ($class ? 'class="alert-warn"': '') .'>' . $message . '</div>';
                }
                $allmessages['warning'] = array();
            }
        } else if ($type == 'info'){
            if (isset($allmessages['info'])){
                foreach ($allmessages['info'] as $message){
                   $messages .= '<div '. ($class ? 'class="alert-pos"': '') .'>' . $message . '</div>';
                }
                $allmessages['info'] = array();
            }
        }
        $messageSession->messages = $allmessages;
        return $messages;

    }


}