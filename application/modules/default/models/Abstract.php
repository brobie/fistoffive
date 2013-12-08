<?php

abstract class Fof_Model_Abstract
{
	protected $_mapper;
	protected $_id;
	
	abstract public function createDataArray();
	
	abstract public function explodeDataArray($data);
	
	public function setPrimary($primary){
		return $this->setId($primary);
	}
	
	public function setId($id){
		$this->_id = $id;
		return $this; 
	}
	
	public function getId(){
		return $this->_id;
	}

	public function setMapper($mapper)
	{
		$this->_mapper = $mapper;
		return $this;
	}

	public function getMapper()
	{
		if (null === $this->_mapper) {
			$this->setMapper($this->_getMapperObject());
		}
		return $this->_mapper;
	}

	public function save()
	{
		$this->getMapper()->save($this);
	}

	


	public function find($id)
	{
		$this->getMapper()->find($id, $this);
		return $this;
	}

	public function fetchAll()
	{
		return $this->getMapper()->fetchAll();
	}

	abstract protected function _getMapperObject();

}
