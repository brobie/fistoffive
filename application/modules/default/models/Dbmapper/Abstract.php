<?php

abstract class Fof_Model_Dbmapper_Abstract
{

	protected $_dbTable;
	protected $_tableName;
	protected $_isDomain = false;

	

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if (is_null($this->_tableName)){
			throw new Exception('Table name is not defined in the dbmapper.');
		}
		if (null === $this->_dbTable) {
			$this->setDbTable($this->_tableName);
		}
		return $this->_dbTable;
	}

	public function save(Fof_Model_Abstract $object)
	{
		if (count($this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY)) != 1){
			throw new Exception('The abstract save cannot be used when there is more than one part to the primary key');
		} else {
			$keyArray = $this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY);
			$primary = $keyArray[1];
		}
		 
		$data = $object->createDataArray();
		$object->explodeDataArray($data);
		if (null === ($id = $object->getId())) {
			unset($data[$primary]);
			$id = $this->getDbTable()->insert($this->_prepareDataForTable($data,$this->getDbTable()));
			$object->setPrimary($id);
		} else {
			$this->getDbTable()->update($this->_prepareDataForTable($data,$this->getDbTable()), array($primary . ' = ?' => $id));
		}
		return $object;
	}

	

	public function find($id, Fof_Model_Abstract $object)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$this->_loadObject($row, $object);
	}

	public function fetchAll()
	{
		if (is_null($this->_modelName)){
			throw new Exception('Model name is not defined int he dbmapper');
		}
		$resultSet = $this->getDbTable()->fetchAll();
		return $this->_loadObjectArray($resultSet, array(), $this->_modelName);
	}

	protected function _loadObject(Zend_Db_Table_Row_Abstract $row, $object){
		$data = array();
		foreach($row->toArray() as $columnName => $columnValue){
			$data[$columnName] = $columnValue;
		}
		$object->explodeDataArray($data);
		$object->setMapper($this);
		return $object;
	}

	protected function _loadObjectArray(Zend_Db_Table_Rowset_Abstract $rows, $collection, $objectType, $collectionKey = null){
		foreach($rows as $row){
			$object = new $objectType();
			if (is_null($collectionKey)){
				$collection[] = $this->_loadObject($row, $object);
			} else {
				$collection[$row[$collectionKey]] = $this->_loadObject($row, $object);
			}
		}
		return $collection;
	}
	
	/**
	 * 
	 */
	protected function _prepareDataForSave($data)
	{
		return $this->_prepareDataForTable($data, $this->getDbTable());
	}
	
	/**
	 *
	 */
	protected function _prepareDataForTable($data, Zend_Db_Table_Abstract $table)
	{
		$fields = $table->info(Zend_Db_Table_Abstract::COLS);
		$tableData = array();
		foreach ($fields as $index => $field) {
				
			if (key_exists($field, $data)) {
				$fieldValue = $data[$field];
				if ($fieldValue instanceof Zend_Db_Expr) {
					$tableData[$field] = $fieldValue;
				} else {
					if (null !== $fieldValue) {
						$tableData[$field] = $fieldValue;
					} 
				}
			} else {
				unset($tableData[$field]);
			}
		}
		return $tableData;
	}
}
