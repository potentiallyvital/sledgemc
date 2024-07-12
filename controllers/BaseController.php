<?php

/**
 * base controller class for sledgeMC
 */
#[\AllowDynamicProperties]
class BaseController
{
        /**
         * base constructor
         */
        function __construct($params = [])
        {
		if (substr(get_class($this), -10) == 'Controller')
		{
			$GLOBALS['SLEDGEMC'] = $this;
		}
        }

	/**
	 * show stuff when we're done
	 */
	function __destruct()
	{
		#$textareas = []; // uncomment to enable html trimming

		if (!empty($this->views))
		{
			$html = implode('', array_reverse($this->views));

			if (isset($textareas))
			{
				$new = [];
				$parts = explode('<textarea ', $html);
				$new[] = array_shift($parts);
				foreach ($parts as $i => $part)
				{
					$part = explode('</textarea>', $part);
					$new[] = '<textarea'.$i.'>';
					$textareas[$i] = '<textarea '.array_shift($part).'</textarea>';
					$new[] = array_shift($part);
				}

				$html = implode('', $new);
			}

			// ajax never needs header/footer
			if ($this->isAjax())
			{
				if (isset($textareas))
				{
					$html = str_replace("\t","\r\n",$html);
					$html = preg_replace('/^\s+|\s+$/m', '', $html);
					foreach ($textareas as $i => $textarea) { $html = str_replace('<textarea'.$i.'>', $textarea, $html); }
				}
				echo $html;
			}
			else
			{
				// input page html into template and display
				$this->data['innards'] = $html;
				$html = $this->viewOnly('/template', true);
				if (isset($textareas))
				{
					$html = str_replace("\t","\r\n",$html);
					$html = preg_replace('/^\s+|\s+$/m', '', $html);
					foreach ($textareas as $i => $textarea) { $html = str_replace('<textarea'.$i.'>', $textarea, $html); }
				}

				echo $html;
			}
		}
	}

        /**
         * is this a proper ajax request?
         */
        function isAjax()
        {
                if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')
                {
                        return false;
                }

                return true;
        }

        /**
         * set app data
         */
        function setData()
        {
                $this->data['title'] = SLEDGEMC_TITLE;
        }

        /**
         * handle incoming http request
         * use url args to determine controller, method, and method values
         */
        function initialize($url)
        {
                // get the URL args
                $args = $url;
                $args = ltrim($args, '/');
                $args = explode('?', $args);
                $args = array_shift($args);
                $args = explode('/', $args);
                foreach ($args as $key => $value)
                {
                        $args[$key] = urldecode($value);
                }
                $uri = $args;

                // set up empty slugs
                for ($i=0; $i<=10; $i++)
                {
                        if (!isset($args[$i]))
                        {
                                $args[$i] = false;
                        }
                }

                // figure out the controller and method
                for ($i=0; $i<=10; $i++)
                {
                        if (empty($controller))
                        {
                                $try = $args[$i];
                                $try = str_replace('-', ' ', $try);
                                $try = strtolower($try);
                                $try = ucwords($try);
                                $try = str_replace(' ', '', $try);
                                $try_path = SLEDGEMC_PATH.'/controllers/'.$try.'.php';
                                if (file_exists($try_path))
                                {
                                        $controller = $try;
                                        $method = $args[($i+1)];
                                        for ($remove=0; $remove<=$i; $remove++)
                                        {
                                                array_shift($args);
                                        }
                                }
                        }
                }

                // default to main controller if not found
                if (empty($controller))
                {
                        $controller = 'Main';
                        $method = $args[0];
                }

		$method = ($method ?: 'index');

                $args['controller'] = slugify($controller);
                $args['method'] = $method;

                // do controller stuff
                $controller .= 'Controller';
                $controller = new $controller();
                $controller->url = BASE_URL.'/'.implode('/', $uri);
                $controller->uri = $uri;
                $controller->data = $args;
                $controller->method($method);
        }

        /**
         * do things before executing a method
         */
        function beforeMethod($method)
        {

        }

        /**
         * do things after executing a method
         */
        function afterMethod($method)
        {

        }

        /**
         * attempt to call a method
         * if method not found, use index
         */
        function method($method = null)
        {
                // strip extension
                $method = pathinfo($method, PATHINFO_FILENAME);

                // set controller global
                $GLOBALS['controller'] = $this;

                // set app data
                $this->setAppUser();
                $this->setData();

                // validate user
                if (!$this->verify())
                {
                        $this->redirect('index');
                }

                // do controller method
                $this->beforeMethod($method);

                $method = str_replace('-', '_', $method);
                if (method_exists($this, $method))
                {
                        array_shift($this->data);
                }
                elseif (method_exists($this, 'index'))
                {
                        $method = 'index';
                }
                else
                {
                        $method = 'main';
                }
                $this->$method(array_shift($this->data), array_shift($this->data), array_shift($this->data), array_shift($this->data), array_shift($this->data));

                $this->afterMethod($method);
        }

        /**
         * default method for the controller
         */
        function main()
        {
                $this->view('index');
        }

        /**
         * redirect to another url in this app
         */
        function redirect($url = null, $unused = true)
        {
                if (stristr($url, 'http'))
                {
                        header('Location: '.$url);
                }
                else
                {
                        header('Location: '.BASE_URL.($url && $url != 'index' ? '/'.ltrim($url, '/') : ''));
                }
                exit;
        }

        /**
         * refresh the page
         */
        function refresh()
        {
		$url = BASE_URL.$_SERVER['REQUEST_URI'];
		$url = explode('?', $url);
		$url = array_shift($url);

                header('Location: '.$url);
                exit;
        }

        /**
         * load a view page
         * include the header if not included yet
         * include the footer by default
         * render the content first so the nav is loaded with updated values (if modifed in a view)
         */
        function view($view = 'index', $header = true)
        {
                // get the view html
                ob_start();
		$this->viewOnly($view);
                $page = ob_get_contents();
                ob_end_clean();

		// keep track of all views
		// we'll show them at the end
		$this->views[] = $page;
        }

	/**
	 * view some HTML without a view page
	 */
	function viewHtml($html)
	{
		$this->data['content'] = $html;

		$html = $this->viewOnly('/template', true);

		echo $html;
	}

        /**
         * include only a view, no header/footer
         */
        function viewOnly($view, $return = false)
        {
                // set variables for view
                $user = $this->user;
                $session = $this->session;
                $data = $this->data;
                extract($data);

                // if the view directory is specified, use the whole path
                // otherwise, assume the view directory matches the controller name (MainController assumes views/main)
                if (stristr($view, '/'))
                {
                        $parts = explode('/', $view);
                        $controller = array_shift($parts);
                        $view = implode('/', $parts);
                }
                else
                {
                        $controller = strtolower(str_replace('Controller', '', get_class($this)));
                }

                // start output buffer if we are returning the view
                if ($return)
                {
                        ob_start();
                }

                // include the view or throw an error
                if (file_exists(SLEDGEMC_PATH.'/views/'.$controller.'/'.$view.'.php'))
                {
                        require SLEDGEMC_PATH.'/views/'.$controller.'/'.$view.'.php';
                }
                elseif ($return)
                {
                        return "<pre>Error: /views/$controller/$view.php not found :(</pre>";
                }
                else
                {
                        echo "<pre>Error: /views/$controller/$view.php not found :(</pre>";
                        return;
                }

                // return output buffer if necessary
                if ($return)
                {
                        $contents = ob_get_contents();
                        ob_end_clean();
                        return $contents;
                }
        }

	/**
	 * get the controller instance
	 */
	static function getInstance()
	{
		if (!empty($GLOBALS['SLEDGEMC']))
		{
			return $GLOBALS['SLEDGEMC'];
		}
		else
		{
			return new BaseController();
		}
	}

	/**
	 * switch to a new controller
	 */
	function switchController($model)
	{
		$controller = new $model();
		foreach (get_object_vars($this) as $key => $value)
		{
			$controller->$key = $value;
		}
		return $controller;
	}
}
