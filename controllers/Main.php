<?php

class MainController extends Controller
{
	/**
	 * everyone can view the main controller
	 */
        function verify()
        {
		return true;
        }
	
        /**
         * the default method, if no other controller/method is found
         */
        function index($controller = null, $method = null, $param = null)
        {
                if ($controller && file_exists(SLEDGEMC_PATH.'/views/main/'.$controller.'.php'))
                {
                        $this->data['title'] = deslugify($controller);

                        $this->view($controller);
                }
                elseif ($this->user)
                {
                        $this->redirect('home');
                }
                else
                {
                        $this->data['title'] = SLEDGEMC_TITLE;

                        $this->view('index');
                }
        }

        /**
         * login page and post handling
         */
        function login()
        {
                $email = strtolower(post('login_email'));
                $password = post('login_keyword');
                if ($email)
                {
                        $user = GameUser::selectOne("SELECT * FROM game_user WHERE email ILIKE '{$email}' OR name ILIKE '{$email}'");
                        if ($user)
                        {
                                $hash = $user->getHashedPassword($password);
				if (empty($user->password))
				{
					$user->setPassword($password)->save();
				}
                                if ($user->password == $hash || $user->password == $password)
                                {
					$user->setAttribute('time_last_login', time());

                                        $this->session->setParent($user)->save();

                                        $this->redirect('home');
                                }
                        }

                        $this->session->flash('Invalid Credentials');
                }

                $this->view('index');
        }

        /**
         * destroy the session
         */
        function logout()
        {
                if ($this->session)
                {
                        $this->session->orphan()->save();
                }

                $this->redirect('index');
        }

        /**
         * create a new user
         */
        function register()
        {
                if (post())
                {
                        $email = post('register_email');
                        $username = post('register_username');
                        $password = post('register_keyword');
                        $confirm = post('register_confirm');

                        $errors = [];

                        $is_npc = ($email == 'npc@potentiallyvital.com');

                        if (!$is_npc)
                        {
                                if (empty($email) || !stristr($email, '.') || !stristr($email, '@') || strlen($email) < 5)
                                {
                                        $errors[] = 'A valid email address is required';
                                }
                                else
                                {
                                        $user = GameUser::getByEmail($email);
                                        if ($user)
                                        {
                                                $errors[] = 'An account with that email address already exists';
                                        }
                                }
                        }

                        if (empty($username))
                        {
                                $errors[] = 'A username is required';
                        }
                        else
                        {
                                $user = GameUser::getByName($username);
                                if ($user)
                                {
                                        $errors[] = 'That username is already taken';
                                }
                        }
                        if (empty($password))
                        {
                                $errors[] = 'A password is required';
                        }
                        elseif ($confirm != $password)
                        {
                                $errors[] = 'Passwords do not match';
                        }

                        if ($errors)
                        {
                                foreach ($errors as $error)
                                {
                                        $this->session->flash($error);
                                }
                        }
                        else
                        {
                                $user = new GameUser();
                                $user->setName($username);
                                $user->setEmail($email);
                                $user->setPassword($password);
				$user->save();

                                $_POST['login_email'] = $_POST['register_email'];
                                $_POST['login_keyword'] = $_POST['register_keyword'];

                                $this->login();
                        }
                }

                $this->view('index');
        }

        /**
         * silly people
         */
        function wp_admin()
        {
                echo '<pre>';
                include SLEDGEMC_PATH.'/views/main/troll.txt';
                echo '</pre>';
        }
}
