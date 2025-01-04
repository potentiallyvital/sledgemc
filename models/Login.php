<?php

class Login extends LoginBase
{
	/**
	 * auto-hash password
	 */
	function setPassword($password = null, $hash = true)
	{
		if ($hash)
		{
			if (!$this->id)
			{
				$this->save();
			}
			$password = $this->getHashedPassword($password);
		}
		return parent::setPassword($password);
	}

	/**
	 * check user input against hashed password
	 */
	function getHashedPassword($password)
	{
		$hashes = [];
		$hashes[] = md5($this->id);
		$hashes[] = md5($this->email);
		$hashes[] = md5($this->created);
		$hashes[] = md5($password);

		asort($hashes);

		$hash = implode('', $hashes);

		return $hash;
	}
}
