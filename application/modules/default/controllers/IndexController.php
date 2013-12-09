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

         $this->view->title = "Fist of Five | Create Your Statement";
        
        $this->view->metaDescription = "Fist of Five, sometimes called Fist to Five is a consensus building tool, generally used by Agile teams.  FistOfFive.com is a way to gain this consensus online.";
        $this->view->metaKeywords = "fist of five, agile, scrum, statement, question";
      
        
        $this->view->bodyId = "body";
         
    }
    
    
}