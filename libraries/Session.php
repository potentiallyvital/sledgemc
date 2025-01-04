<?php

/**
 * handle a users session
 * be able to do functions and stuff
 */
#[\AllowDynamicProperties]
class Session
{
	var $account = null;
	var $data = [];
	var $flash = [];

	/**
	 * load up session on construct
	 */
	function __construct($session_key = null)
	{
		$this->data = [];
		foreach ($_SESSION as $key => $value)
		{
			$this->data[$key] = $value;
		}

		if (empty($this->data) || $session_key != $this->data['session_key'])
		{
			$this->data = [];
			$this->data['session_key'] = $session_key;
			$this->data['created'] = date('Y-m-d H:i:s');
		}
		$this->data['updated'] = date('Y-m-d H:i:s');

		if (empty($this->data['flash']))
		{
			$this->data['flash'] = [];
		}
	}

	/**
	 * save on destroy
	 */
	function __destruct()
	{
		foreach ($this->data as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
		foreach ($_SESSION as $key => $value)
		{
			if (empty($this->data[$key]))
			{
				unset($_SESSION[$key]);
			}
		}
	}

	/**
	 * return my user
	 */
	function getAccount()
	{
		if (!$this->account)
		{
			if (!empty($this->data['account_id']))
			{
				$this->account = Account::getById($this->data['account_id']);
			}
		}

		return $this->account;
	}

	/**
	 * add session var
	 */
	function setData($key, $var)
	{
		$this->data[$key] = $var;
	}

	/**
	 * notify a user of something
	 */
	function flash($message)
	{
		$this->data['flash'][] = $message;
	}
}
