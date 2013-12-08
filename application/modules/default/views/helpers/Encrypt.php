<?php

require_once 'Zend/View/Helper/Abstract.php';

class Fof_Helper_Encrypt extends Zend_View_Helper_Abstract
{
    public function encrypt($string){
        return preg_replace('/=+$/','',base64_encode(pack('H*',md5($string))));
    }


}