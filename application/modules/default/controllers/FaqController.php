<?php

require_once 'AbstractController.php';

class FaqController extends AbstractController
{
	/**
	 * This action is the main landing page for the site.
	 */
    public function indexAction()
    {
    
        $this->view->title = "Fist of Five | FAQ";
        
        $this->view->metaDescription = "The most frequently asked questions";
        $this->view->metaKeywords = "fist of five, agile, scrum, statement, question";
      
        
        $this->view->bodyId = "body";
         
    }
    
    
}