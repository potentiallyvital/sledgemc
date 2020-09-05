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
