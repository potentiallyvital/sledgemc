<?php

class Children extends ChildrenBase
{
	/**
	 * set my parent relationship
	 */
	function setParent($object)
	{
		$this->setParentId($object->id);
		$this->setParentTable($object::TABLE_NAME);
		$this->setParentClass($object::CLASS_NAME);
	}

	/**
	 * set my child relationship
	 */
	function setChild($object)
	{
		$this->setChildId($object->id);
		$this->setChildTable($object::TABLE_NAME);
		$this->setChildClass($object::CLASS_NAME);
	}
}
