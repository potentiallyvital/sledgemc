<?php

/**
 * customized controller for this app
 */
class Controller extends BaseController
{
        /**
         * verify this user can see this controller
         * default to allow visibility to everyone
         */
        function verify()
        {
		return true;
        }

        /**
         * set the user from session
         */
        function setAppUser()
        {
                $this->setSession();
                $this->setUser();

                $this->data['visitor'] = ($this->user ?: $this->session);
		$this->data['user'] = $this->user;
        }

        /**
         * set the current user
         */
        function setUser()
        {
                $this->user = null;

                if (!empty($this->session))
                {
			$this->user = $this->session->user;
                }
        }

        /**
         * set the current session
         */
        function setSession()
        {
		session_start();

                $key = session_id().'_'.$_SERVER['REMOTE_ADDR'];

		$this->session = new Session($key);
        }

        /**
         * set all tables for each page
         */
        function setData()
        {
                parent::setData();

		$title = $this->data['controller'];
		if ($this->data['method'] != 'index')
		{
			$title = $this->data['method'];
		}
		$title = deslugify($title);

                $this->data['title'] = $title;
        }

	/**
	 * flash a message to a user
	 */
	function flash($message)
	{
		$this->session->flash[] = $message;
	}

	/**
	 * user is being mischievous
	 */
	function error($message)
	{
		$this->flash($message);

		// TODO punishment for bad behavior
	}
}
