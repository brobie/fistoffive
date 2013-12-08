<?php

class Fof_Model_Answer extends Fof_Model_Abstract
{
	
	protected $_statementId;
	protected $_answer;
	protected $_name;
	protected $_comment;

	public function createDataArray(){
		$data = array();
		$data['id'] = $this->_id;
		$data['statement_id'] = $this->_statementId;
		$data['answer'] = $this->_answer;
		$data['name'] = $this->_name;
		$data['comment'] = $this->_comment;
		
		return $data;
	}
	
	public function explodeDataArray($data){
		$this->_id = $data['id'];
		$this->_statementId = $data['statement_id'];
		$this->_answer = $data['answer'];
		$this->_name = $data['name'];
		$this->_comment = $data['comment'];
		
		return $this;
	}
	
	public function getStatementId(){
		return $this->_statement_id;
	}
	
	public function setStatementId($statementId){
		$this->_statementId = $statementId;
		return $this;
	}
	
	public function getAnswer(){
		return $this->_answer;
	}
	
	public function setAnswer($answer){
		$this->_answer = $answer;
		return $this;
	}
	
	public function getName(){
		return $this->_name;
	}
	
	public function setName($name){
		$this->_name = $name;
		return $this;
	}
	
	public function getComment(){
		return $this->_comment;
	}
	
	public function setComment($comment){
		$this->_comment = $comment;
		return $this;
	}
	
	
	protected function _getMapperObject(){
		return new Fof_Model_Dbmapper_Answer();
	}

	public function getAllAnswersByStatementId($statementId)
	{
		return $this->getMapper()->fetchAllByStatementId($statementId);
	}


}
