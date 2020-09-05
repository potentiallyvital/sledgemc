<?php

/**
 * handle a users session
 * be able to do functions and stuff
 */
class Session
{
	var $user = null;
	var $data = [];
	var $flash = [];

	/**
	 * load up session on construct
	 */
	public function __construct($session_key = null)
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

		$this->flash = [];
	}

	/**
	 * save on destroy
	 */
	public function __destruct()
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
	 * notify a user of something
	 */
	function flash($message)
	{
		$this->flash[] = $message;
	}
}
