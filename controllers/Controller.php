<?php

/**
 * customized controller for this app
 */
class Controller extends BaseController
{
	/**
	 * default all controllers other than main as requiring a user
	 */
	function verify($method)
	{
		if (!$this->account)
		{
			return false;
		}

		return parent::verify($method);
	}
}
