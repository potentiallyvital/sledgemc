<?php

class MainController extends Controller
{
	/**
	 * everyone can view the main controller
	 */
	function verify($method)
	{
		return true;
	}

	/**
	 * login page and post handling
	 */
	function login()
	{
		$email = strtolower(post('sledge_login_email'));
		$password = post('sledge_login_keyword');
		if ($email)
		{
			$login = Login::selectOne("SELECT * FROM login WHERE email = '{$email}'");
			if ($login)
			{
				$hash = $login->getHashedPassword($password);
				if (empty($login->password))
				{
					$login->setPassword($password)->save();
				}

				$valid = ($login->password == $hash);
				$valid_temp = ($login->temporary_pass == $password);

				if ($valid || $valid_temp)
				{
					$account = $login->getParent('Account');

					$this->session->setData('account_id', $account->id);

					$this->flash('Welcome back');

					$this->redirect('home');
				}
				else
				{
					$login->increment('invalid_logins');
				}
			}

			$this->flash('Invalid Credentials');
		}

		$this->view('index');
	}

	/**
	 * destroy the session
	 */
	function logout()
	{
		$this->session->setData('account_id', null);

		$this->redirect('index');
	}

	/**
	 * create a new user
	 */
	function register()
	{
		if (post())
		{
			$email = strtolower(post('sledge_register_email'));
			$username = post('sledge_register_username');
			$password = post('sledge_register_keyword');
			$confirm = post('sledge_register_confirm');

			$errors = [];

			if (empty($email) || !stristr($email, '.') || !stristr($email, '@') || strlen($email) < 5)
			{
				$errors[] = 'A valid email address is required';
			}
			else
			{
				$user = Account::getByEmail($email);
				if ($user)
				{
					$errors[] = 'An account with that email address already exists';
				}
			}

			if (empty($username))
			{
				$errors[] = 'A username is required';
			}
			else
			{
				$user = Account::getByUsername($username);
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
					$this->flash($error);
				}
			}
			else
			{
				$user = new Account();
				$user->setUsername($username);
				$user->setEmail($email);
				$user->setPassword($password);
				$user->save();

				$_POST['sledge_login_email'] = $_POST['sledge_register_email'];
				$_POST['sledge_login_keyword'] = $_POST['sledge_register_keyword'];

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
