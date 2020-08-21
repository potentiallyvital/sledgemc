<?php

/**
 * customized controller for this app
 */
class Controller extends BaseController
{
        /**
         * verify this user can see this controller
         * default to exclude visitors
         */
        function verify()
        {
                return $this->character;
        }

        /**
         * redirect for objects
         */
        function redirect($to = null, $method = null)
        {
                if (is_object($to))
                {
                        $to = $to->getExploreLink($method);
                }

                return parent::redirect($to);
        }

	/**
	 * check if im doing things inside the admin portal
	 */
	function isAdminApp()
	{
		return ($this->data['controller'] == 'admin');
	}

        /**
         * show a date based on timezone
         */
        function showDate($date, $format = 'Y-m-d')
        {
                if (empty($date))
                {
                        return 'at some point in the past';
                }
                elseif (preg_replace('/[^0-9]/', '', $date) == $date)
                {
                        $timestamp = $date;
                }
                else
                {
                        $timestamp = strtotime($date);
                }

                $timezone = $this->session->timezone;
                if ($timezone)
                {
                        $date = new DateTime('now', new DateTimeZone($timezone));
                        $date->setTimestamp($timestamp);

                        return $date->format($format);
                }
                else
                {
                        return date($format, $timestamp);
                }
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
		$this->data['character'] = $this->character;
        }

        /**
         * set the current user
         */
        function setUser()
        {
                $this->user = null;
                $this->user_id = null;

		$this->character = null;
		$this->character_id = null;

                if (!empty($this->session) && !empty($this->session->parent_id))
                {
                        $this->user = GameUser::getById($this->session->parent_id);
                        if (!empty($this->user))
                        {
                                $this->user_id = $this->session->parent_id;

				$character = $this->user->getCharacter();
				if ($character)
				{
					$this->character = $character;
					$this->character_id = $character->id;
				}

                                $this->checkTimeZoneUser();
                        }
                }
        }

        /**
         * set the current session
         */
        function setSession()
        {
                $key = session_id().'_'.$_SERVER['REMOTE_ADDR'];

                $session = Session::getBySessionKey($key, true);
                if (!$session)
                {
                        $session = new Session();
                        $session->setSessionKey($key);
                }

		$session->setIp($_SERVER['REMOTE_ADDR']);
		$session->setBrowser($_SERVER['HTTP_USER_AGENT']);
		$session->setReferrer((isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ''));
                $session->setModified(date('Y-m-d H:i:s'));
		$session->save();

                $this->session = $session;
                $this->session_id = $session->id;

                $this->checkTimeZoneSession();
        }

        /**
         * check if a timezone is set, if not force JS to give it to us and redirect
         */
        function checkTimeZoneSession()
        {
return;
                if (empty($_POST) && empty($this->session->timezone))
                {
                        if (!empty($_GET['timezone_offset']))
                        {
                                $this->session->setTimezone(null);

                                $offset = ($_GET['timezone_offset'] * 60) * -1;
                                $zones = timezone_abbreviations_list();
                                foreach ($zones as $zone => $sub_zones)
                                {
                                        foreach ($sub_zones as $sub_zone)
                                        {
                                                if (empty($this->session->timezone) && $sub_zone['offset'] == $offset)
                                                {
                                                        $this->session->setTimezone($sub_zone['timezone_id']);
                                                }
                                        }
                                }

                                if (empty($this->session->timezone))
                                {
                                        $this->session->setTimezone('America/Chicago');
                                }

                                $this->session->save();

                                if (!empty($_GET['redirect']))
                                {
                                        header('Location: '.$_GET['redirect']);
                                }
                                else
                                {
                                        $this->redirect('index');
                                }
                        }
                        else
                        {
                                ?>
                                <!DOCTYPE html>
                                <html>
                                        <head></head>
                                        <body>
                                                <script>
                                                        var date = new Date();
                                                        var offset = date.getTimezoneOffset();
                                                        window.location.href = "?timezone_offset="+offset+"&redirect="+window.location.href;
                                                </script>
                                        </body>
                                </html>
                                <?php
                        }
                        exit;
                }
        }

        /**
         * see if the users time zone has changed
         * uses APIs and more costly than the free JS
         */
        function checkTimeZoneUser()
        {
return;
                if (empty($this->user->timezone) || $this->user->timezone != $this->session->timezone)
                {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $result = json_decode(file_get_contents('https://cis.texaselectricityratings.com/index.php/api/ip/'.$ip));
                        if (!empty($result) && !empty($result->timezone) && !empty($result->timezone->id))
                        {
                                $this->user->setTimezone($result->timezone->id);
                                $this->user->setCity($result->city);
                                $this->user->setState($result->region_name);
                                $this->user->setZip($result->zip);
                                $this->user->setCountry($result->country_name);
                                $this->user->setLatitude($result->latitude);
                                $this->user->setLongitude($result->longitude);
                                $this->user->save();
                        }
                }
        }

        /**
         * get the current session
         */
        function getSession()
        {
                if (empty($this->session) && !empty($this->session_id))
                {
                        $this->session = Session::getById($this->session_id);
                }

                return $this->session;
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
                $this->data['left_box'] = '';
                $this->data['right_box'] = '';

                if ($this->character)
                {
			if (!$this->isAjax())
			{
				$this->character->save(true);
			}
                }
        }

        /**
         * flash an error
         */
        function error($string, $redirect = true)
        {
                if ($this->user)
                {
                        $this->user->increment('mischief');
                }

		$params = [];
		$params['uri'] = $_SERVER['REQUEST_URI'];
		$params['get'] = $_GET;
		$params['post'] = $_POST;

                $message = new MessageFlashError();
                $message->setParent(($this->user ?: $this->session));
                $message->setDescription($string);
		$message->setParams(json_encode($params));
		$message->setMethod($this->data['controller'].'/'.$this->data['method']);
		$message->setBacktrace(backtrace(true));
                $message->save(true);

                if ($this->user && $redirect)
                {
			$this->updateUI();
                }
        }

        /**
         * i'm not the fancy ajax version
         */
        function isFancy()
        {
                return false;
        }
}
