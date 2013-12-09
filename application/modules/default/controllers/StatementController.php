<?php

require_once 'AbstractController.php';

class StatementController extends AbstractController
{

	public function createAction(){
		$statementText = $this->getRequest()->getParam('statement');
		$statement = new Fof_Model_Statement();
		$statement->setStatement($statementText);
		$statement->save();
		$this->_redirect('/statement/view/id/'.$statement->getCode());
	}
	
	public function publishAction(){
		$statementCode = $this->getRequest()->getParam('id');
		$statement = new Fof_Model_Statement();
		$statement->findByCode($statementCode);
		$statement->setStatus(Fof_Model_Statement::STATEMENT_STATUS_PUBLISHED);
		$statement->save();
		$this->_redirect('/statement/view/id/'.$statement->getCode());
	}
	
	public function answerCreateAction(){
		$statementCode = $this->getRequest()->getParam('id');
		
		$answerText = $this->getRequest()->getParam('answer');
		$name = $this->getRequest()->getParam('name');
		$comment = $this->getRequest()->getParam('comment');
				
		$statement = new Fof_Model_Statement();
		$statement->findByCode($statementCode);

		if (!$answerText || !$name){
			if (!$answerText){
				$this->addErrorMessage('You have to select an answer.');
			}
			if (!$name){
				$this->addErrorMessage('You have to enter a name.');
			}
			$this->view->statement = $statement;
			$this->render('answer');
			return;
		}
		
		$answer = new Fof_Model_Answer();
		$answer->setAnswer($answerText);
		$answer->setComment($comment);
		$answer->setName($name);
		$answer->setStatementId($statement->getId());
		
		
		$answer->save();
		
		$this->_redirect('/statement/view/id/'.$statement->getCode());
	}
	
	/**
	 * This action is the main landing page for the site.
	 */
    public function viewAction()
    {
    	$statementCode = $this->getRequest()->getParam('id');
    	$statement = new Fof_Model_Statement();
    	$statement->findByCode($statementCode);
    	
    	$this->view->statement = $statement;
    	$this->view->answers = $statement->getAnswers();
    	$this->view->average = $statement->getAnswersAverage();;
    	$this->view->decision = $statement->getDecision();
    	$this->view->domain = $_SERVER['HTTP_HOST'];
    	
    	
        $this->view->title = "Fist of Five | Viewing the Statement";
        
        $this->view->metaDescription = "View the statement along with its answers and statistics about the statement.";
        $this->view->metaKeywords = "fist of five, agile, scrum, statement, question";
        
        $this->view->bodyId = "body";
    }
    
    /**
     * This action is the main landing page for the site.
     */
    public function answerAction()
    {
    	
    	$statementCode = $this->getRequest()->getParam('id');
    	$statement = new Fof_Model_Statement();
    	$statement->findByCode($statementCode);
    	 
    	 
    	$this->view->statement = $statement;
    	
    	 $this->view->title = "Fist of Five | Answer the Statement";
        
        $this->view->metaDescription = "Answer the statement with a number between 1 and 5.  It is required to enter your name for maximum transparency.  The comment is optional.";
        $this->view->metaKeywords = "fist of five, agile, scrum, statement, question";
      
    	$this->view->bodyId = "body";
    }
    
    
    
    
}