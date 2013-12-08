<?php

class Fof_Model_Dbmapper_Answer extends Fof_Model_Dbmapper_Abstract
{
	protected $_tableName = 'Fof_Model_Dbtable_Answer';
	protected $_modelName = 'Fof_Model_Answer';

	public function fetchAllByStatementId($statementId)
	{
		$select = $this->getDbTable()->select()
		->from(array('answer' => 'answer'), '*')
		->where('statement_id = ?', $statementId);
			
		$rows = $this->getDbTable()->fetchAll($select);
		return $this->_loadObjectArray($rows, array(), $this->_modelName);
	
	}

}
