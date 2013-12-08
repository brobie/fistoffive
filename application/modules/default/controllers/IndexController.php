<?php

require_once 'AbstractController.php';

class IndexController extends AbstractController
{
	/**
	 * This action is the main landing page for the site.
	 */
    public function indexAction()
    {
    	$this->view->navigationPhtml = 'empty.phtml';
    	$this->view->headerPhtml = 'empty.phtml';

        $this->view->title = "Fof | Main Page";
        
        $this->view->metaDescription = "Fof Main Page Description";
        $this->view->metaKeywords = "Fof meta keywords";
        
        $this->view->bodyId = "body";
         
    }
    
    
}