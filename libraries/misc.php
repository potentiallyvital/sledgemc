<?php

// this file contains application specific global functions
// used for session handling, url handling, place handling, etc

/**
 * return the session object for the user
 *
 * @return Session object
 */
function session()
{
	if (isset($GLOBALS['session']))
	{
		return $GLOBALS['session'];
	}

	$id = session_id();
	$session = Session::getBySessionKey($id, true);
	if (!$session)
	{
		$session = new Session();
		$session->setSessionKey($id);
		$session->save();
	}

	$GLOBALS['session'] = $session;
	return $session;
}

/**
 * convert a string into a pretty url slug
 * ex:
 * input: 187 Quick Foxes - all jumping & stuff
 * output: 187-quick-foxes-all-jumping-and-stuff
 *
 * @param $string - string (string to convert)
 *
 * @return string
 */
function slugify($string, $allow_slash = false)
{
	$string = htmlspecialchars_decode($string ?: '');
	$string = strip_tags($string);
	$string = strtolower($string);
	if (!$allow_slash)
	{
		$string = str_replace('/', ' and ', $string);
	}
	$string = str_replace('&', ' and ', $string);
	$string = str_replace(['[',']','_'], '-', $string);
	$string = preg_replace('|[^-/ a-zA-Z0-9]|', '', $string);
	$string = str_replace(' ', '-', $string);
	while (stristr($string, '--'))
	{
		$string = str_replace('--', '-', $string);
	}
	$string = ltrim($string, '-');
	$string = rtrim($string, '-');
	return $string;
}

/**
 * super slugify a string for searching and similarity
 * remove things like "the", "an"
 * make stuff phoentic
 * TODO - check for common misspellings
 */
function superslug($string, $already_slug = false)
{
	if (!$already_slug)
	{
		$string = slugify($string);
	}

	// remove filler words
	$remove = ['a','an','as','and','of','with','the','but','featuring','feat','ft'];
	$replace = [
		'one'=>1, 'two'=>2, 'three'=>3, 'four'=>4, 'five'=>5, 'six'=>6, 'seven'=>7, 'eight'=>8, 'nine'=>9, 'ten'=>10,
		'then'=>'than',
	];
	$parts = explode('-', $string);
	foreach ($parts as $i => $part)
	{
		if (in_array($part, $remove))
		{
			// remove filler words
			unset($parts[$i]);
		}
		elseif (isset($replace[$part]))
		{
			// replace common words
			$parts[$i] = $replace[$part];
		}
		else
		{
			// remove trailing s/d/etc
			if (!empty($parts[$i]) && strlen($parts[$i]) >= 3)
			{
				$parts[$i] = rtrim($parts[$i], 's');
				$parts[$i] = rtrim($parts[$i], 'd');
				$parts[$i] = rtrim($parts[$i], 'g');
				$parts[$i] = rtrim($parts[$i], 'e');
			}
		}
	}
	$string = implode('-', $parts);

	// help with typos
	$phoenitic = [
		['youre','ur'],
		['your','ur'],
		['eart','art'],
		['q','k'],
		['x','ks'],
		['ah','a'],
		['ck','k'],
		['kn','n'],
		['gn','n'],
		['pn','n'],
		['mb','m'],
		['ph','f'],
		['ae','e'],
		['ea','e'],
		['wh','w'],
		['wr','r'],
		['ie','y'],
		['ei','y'],
		['oi','oy'],
		['dz','ds'],
		['qu','kw'],
		['ca','ka'],
		['ce','se'],
		['ci','si'],
		['co','ko'],
		['cu','ku'],
		['igh','i'],
		['chr','kr'],
		['rhy','ri'],
		['ove','uv'],
		['ead','ed'],
		['tch','ch'],
		['sch','sh'],
		['url','irl'],
		['erl','irl'],
		['oyn','oin'],
		['urg','erg'],
		['tion','shn'],
		['tian','shn'],
		['cion','shn'],
		['cian','shn'],
		['sion','shn'],
		['sian','shn'],
		['ough','u'],
		['augh','o'],
		['gh','g'],
	];
	foreach ($phoenitic as $set)
	{
		$string = str_replace($set[0], $set[1], $string);
	}

	// remove duplicate consecutive letters
	$parts = str_split($string);
	$string = [];
	$last = null;
	foreach ($parts as $part)
	{
		if ($part != $last)
		{
			$string[] = $part;
		}
		$last = $part;
	}
	$string = implode('', $string);

	return $string;
}

/**
 * convert a url slug into a pretty string
 *
 * @param $slug - string (url part to convert)
 * @para $ucfirst - boolean (true = capital first letter only, false = cap all words)
 *
 * @return string
 */
function deslugify($string, $ucfirst = false)
{
	$string = str_replace('/', ' | ', $string);
	$string = str_replace(['-','_'], ' ', $string);
	$string = str_replace(' &amp; ', ' & ', $string);
	$string = str_replace(' and ', ' & ', $string);
	if ($ucfirst)
	{
		$string = ucfirst($string);
	}
	else
	{
		$string = ucwords($string);
	}
	return $string;
}

/**
 * add X tabs to all lines of a string
 * mostly used for pretty source code
 */
function tabify($string, $tabs = 1, $tab = '    ')
{
	#$tabbed = "\r\n";
	$tabbed = '';

	$string = explode(PHP_EOL, rtrim($string));

	while (count($string) > 0)
	{
		$part = array_shift($string);
		if (strlen(trim($part)) > 0)
		{
			$tabbed .= PHP_EOL;
			for ($i=1; $i<=$tabs; $i++)
			{
				$tabbed .= $tab;
			}
			$tabbed .= $part;
		}
	}

	$tabbed .= "\r\n";

	return $tabbed;
}

/**
 * make all elements of a string single line
 */
function oneline($string)
{
	$line = [];

	$string = explode(PHP_EOL, $string);

	while (count($string) > 0)
	{
		$part = trim(array_shift($string));
		if ($part)
		{
			$line[] = $part;
		}
	}

	$line = implode(' ', $line);

	return $line;
}

/**
 * convert a table name or string into a class name
 *
 * @param $string - string to convert
 *
 * @return string
 */
function class_name($string)
{
	return str_replace(' ', '', ucwords(deslugify($string)));
}

/**
 * return the controller object
 *
 * @return Controller object
 */
function controller()
{
	return (isset($GLOBALS['controller']) ? $GLOBALS['controller'] : null);
}

/**
 * display a monetary value
 *
 * @param $amount - money amount
 *
 * @return string
 */
function money($amount)
{
	if (is_numeric($amount))
	{
		$amount = decimal($amount);
	}

	$amount = '<small>$</small>'.$amount;
	$amount = str_replace(',', '<small>,</small>', $amount);

	return '<span class="money">'.$amount.'</span>';
}

/**
 * display a pretty decimal
 */
function decimal($value)
{
	if (is_numeric($value) || stristr($value, '.'))
	{
		$value = number_format($value, 2);
		$value = explode('.', $value);
		$decimal = array_pop($value);
		$value = $value[0].'<small><sup>.'.$decimal.'</sup></small>';
	}

	return $value;
}

/**
 * normalize artist names
 */
function artistify($name)
{
	$ft = ['and','ft','feat','featuring','with'];
	foreach ($ft as $to)
	{
		$name = str_replace("-{$to}-", "-ft-", $name);
	}
	return $name;
}
