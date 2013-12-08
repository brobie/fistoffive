<?php

class Fof_Model_Dbmapper_Statement extends Fof_Model_Dbmapper_Abstract
{
	protected $_tableName = 'Fof_Model_Dbtable_Statement';
	protected $_modelName = 'Fof_Model_Statement';

	public function findByCode($code, Fof_Model_Statement $statment)
	{
		$row =  $this->getDbTable()->fetchRow($this->getDbTable()->select()->where('code = ?', $code));
		if (null === $row){
			return;
		}

		$this->_loadObject($row, $statment);
	}



	public function delete($id){
		$where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);

		return $this->getDbTable()->delete($where);
	}


}
