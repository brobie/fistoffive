<?php

class Fof_Model_Statement extends Fof_Model_Abstract
{

	protected $_statement;
	protected $_code;
	protected $_status;
	protected $_answers;

	const STATEMENT_STATUS_DRAFT = 'draft';
	const STATEMENT_STATUS_PUBLISHED = 'published';
	const STATEMENT_STATUS_ARCHIVED = 'archived';

	const STATEMENT_DECISION_UNANSWERED = 'unanswered';
	const STATEMENT_DECISION_UNDECIDED = 'undecided';
	const STATEMENT_DECISION_POSITIVE = 'positive';
	const STATEMENT_DECISION_MEH = 'meh';
	const STATEMENT_DECISION_NEGATIVE = 'negative';

	public function createDataArray(){
		$data = array();
		$data['id'] = $this->_id;
		$data['statement'] = $this->_statement;
		$data['code'] = $this->_code;
		if ($data['code'] === null){
			$data['code'] = Fof_Model_Util::getRandomString(10);
		}
		$data['status'] = $this->_status;
		if ($data['status'] === null){
			$data['status'] = Fof_Model_Statement::STATEMENT_STATUS_DRAFT;
		}
		return $data;
	}

	public function explodeDataArray($data){
		$this->_id = $data['id'];
		$this->_statement = $data['statement'];
		$this->_code = $data['code'];
		$this->_status = $data['status'];
		return $this;
	}

	public function getStatement(){
		return $this->_statement;
	}

	public function setStatement($statement){
		$this->_statement = $statement;
		return $this;
	}

	public function getCode(){
		return $this->_code;
	}

	public function setCode($code){
		$this->_code = $code;
		return $this;
	}

	public function getStatus(){
		return $this->_status;
	}

	public function setStatus($status){
		$this->_status = $status;
		return $this;
	}


	protected function _getMapperObject(){
		return new Fof_Model_Dbmapper_Statement();
	}

	public function findByCode($code)
	{
		$this->getMapper()->findByCode($code, $this);
		return $this;
	}

	public function getAnswers(){
		if ($this->_answers === null){
			$answer = new Fof_Model_Answer();
			$this->_answers = $answer->getAllAnswersByStatementId($this->getId());
		}
		return $this->_answers;
	}

	public function getAnswersTotal(){
		$total = 0;
		foreach($this->getAnswers() as $answer){
			$total += $answer->getAnswer();
		}
		return $total;
	}

	public function getDecision(){
	
		$decision = self::STATEMENT_DECISION_UNANSWERED;
		$low = false;
		$three = false;
		$high = false;
		foreach($this->getAnswers() as $answer){
			switch ($answer->getAnswer()){
				case 1:
					$low = true;
					break;
				case 2:
					$low = true;
					break;
				case 3:
					$three = true;
					break;
				case 4:
					$high = true;
					break;
				case 5:
					$high = true;
					break;
			}
			if ($three && !$low && !$high){
				$decision = self::STATEMENT_DECISION_MEH;
			}
			if ($low && !$high){
				$decision = self::STATEMENT_DECISION_NEGATIVE;
			}
			if (!$low && $high){
				$decision = self::STATEMENT_DECISION_POSITIVE;
			}
			if ($low && $high){
				$decision = self::STATEMENT_DECISION_UNDECIDED;
			}
		}
		return $decision;
	}

	public function getAnswersAverage(){
		if (count($this->getAnswers()) === 0){
			return 0;
		}
		return $this->getAnswersTotal() / count($this->getAnswers());
	}

	public function delete()
	{
		return $this->getMapper()->delete($this->getId());
	}


}
