<?php

// this file includes global functions that should have been included in php but arent
// string manipulation, date handlers, input validators, etc

function months($short = false)
{
        $months = [
                0 => 'Month',
                1 => 'January',
                2 => 'February',
                3 => 'March',
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
        ];

        if ($short)
        {
                foreach ($months as $number => $name)
                {
                        $months[$number] = substr($name, 0, 3);
                }
        }

        return $months;
}

function days()
{
        $days = [];
        $days[0] = 'Day';

        for ($day=1; $day<=31; $day++)
        {
                $days[$day] = st($day);
        }

        return $days;
}

function years()
{
        $years = [];
        $years[0] = 'Year';

        for ($year=date('Y'); $year>=date('Y')-100; $year--)
        {
                $years[$year] = $year;
        }

        return $years;
}

function camelCase($string, $uppercase_first = false)
{
        $string = preg_replace('/[^a-z0-9]+/i', ' ', $string);
        $string = ucwords(trim($string));
        $string = lcfirst(str_replace(' ', '', $string));
        if ($uppercase_first){
                $string = ucwords($string);
        }
        return $string;
}

function unCamelCase($string)
{
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
        $strings = $matches[0];
        foreach ($strings as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $strings);
}

function track($message, $die = false)
{
        $backtrace = debug_backtrace();
        $caller = array_shift($backtrace);
        if ($die)
        {
                $caller = array_shift($backtrace);
        }
        $tracker = debug('track() called in <b>'.$caller['file'].'</b> line num <b>'.$caller['line'].'</b>');

        if (empty($GLOBALS['debug_time']))
        {
                $GLOBALS['debug_time'] = microtime(true);

                echo $tracker;
                echo $message;
        }
        else
        {
                $end = microtime(true);
                $time = $end - $GLOBALS['debug_time'];

                $GLOBALS['debug_time'] = microtime(true);

                echo ' - <big><b>'.number_format($time, 4).'</b></big> seconds';
                echo '<hr />';
                echo $tracker;
                echo $message;
        }

        if ($die)
        {
                die();
        }
}

function done()
{
        track('done', true);
}

function debug($string, $style = true)
{
        if (WEB)
        {
                if ($style)
                {
                        return '<pre style="color:rgb(150,150,150);font-size:12px;">'.$string.'</pre>';
                }
                else
                {
                        return '<pre>'.$string.'</pre>';
                }
        }
        else
        {
                return strip_tags($string);
        }
}

function dump($what, $die = true)
{
        $backtrace = debug_backtrace();
        $caller = array_shift($backtrace);

        $what = trim(print_r($what, true));

        if (WEB)
        {
                $what = str_replace('[', '[<b style="color:rgb(200,0,0);">', $what);
                $what = str_replace(']', '</b>]', $what);

                echo "<div class='left'>";
                echo "<pre>";
        }

        echo "\r\n\r\n";
        echo '"';
        echo $what;
        echo '"';
        echo "\r\n\r\n";
        echo debug('dump() called in <b>'.$caller['file'].'</b> line num <b>'.$caller['line'].'</b>');

        if (WEB)
        {
                echo "<br />";
                echo "<br />";
        }
        else
        {
                echo "\r\n";
                echo "\r\n";
        }

        backtrace();
        echo "\r\n";

        if ($die)
        {
                die();
        }
        elseif (WEB)
        {
                echo "</pre>";
                echo "</div>";
        }
}

function saveFile($path, $contents)
{
        //    if (is_file($path)) {
        //        unlink($path);
        //    }
        file_put_contents($path, $contents);
        chmod($path, 0777);
        //    chown($path, 'nwasson');
        //    chgrp($path, 'boun03admin');
}

function fileExtension($path)
{
        $type = explode('.', $path);
        return array_pop($type);
}

function listFiles($directory)
{
        $files = scandir($directory);
        unset($files[0]);
        unset($files[1]);
        sort($files);
        foreach ($files as $key => $name){
                $files[$key] = $directory.'/'.$name;
        }
        return $files;
}

function locateFile($base_directory, $file)
{
        $directories = array();
        $files = listFiles($base_directory);
        foreach ($files as $sub_file){
                if (is_dir($sub_file)){
                        $directories[] = $sub_file;       
                } elseif (stristr($sub_file, '/'.$file)){
                        return $sub_file;
                }
        }
        foreach ($directories as $directory){
                $found = locateFile($directory, $file);
                if ($found){
                        return $found;
                }
        }  
        return false;
}

function _die($message = null)
{
        if ($message){
                echo $message;
        }
        backtrace();
        //$user = user();
        //if ($user){
        //    setElement('money', '$'.number_format($user->getAttribute('money'), 2));
        //}
        die();
}

function median($num1, $num2)
{
	$offset = 0;
	if ($num1 < 0)
	{
		$offset = $num1 * -1;

		$num1 += $offset;
		$num2 += $offset;
	}

	$middle = round(($num1+$num2)/2);
	$middle -= $offset;

	return $middle;
}

function absdiff($num1, $num2)
{
        if ($num1 == $num2){
                $diff = 0;
        } elseif ($num1 >= 0 && $num2 >= 0){
                if ($num1 >= $num2){
                        $diff = $num1-$num2;
                } else {
                        $diff = $num2-$num1;
                }
        } elseif ($num1 <= 0 && $num2 <= 0){
                if ($num1 >= $num2){
                        $diff = abs($num2)-abs($num1);
                } else {
                        $diff = abs($num1)-abs($num2);
                }
        } elseif ($num1 >= 0 && $num2 <= 0){
                $diff = $num1+abs($num2);
        } elseif ($num1 <= 0 && $num2 >= 0){
                $diff = abs($num1)+$num2;
        } else {
                die("i cant figure out the absolute difference between $num1 and $num2");
        }
        return $diff;
}

function countdown($date, $callback = 'refresh')
{
	if (!is_numeric($date))
        {
                $date = strtotime($date);
        }

	if ($date <= time())
	{
		return 'now';
	}
	else
	{
		$difference = $date - time();

		$days = floor($difference/60/60/24);
		$difference -= ($days*60*60*24);

		$hours = floor($difference/60/60);
		$difference -= ($hours*60*60);

		$minutes = floor($difference/60);
		$difference -= ($minutes*60);

		$seconds = floor($difference);

		$time = '';
		if ($days > 0)
		{
			$time .= '<span>'.$days.'d</span> ';
			$hours = ($hours < 10 ? '0'.$hours : $hours);
		}
		if ($hours > 0)
		{
			$time .= '<span>'.$hours.'</span>:';
			$minutes = ($minutes < 10 ? '0'.$minutes : $minutes);
		}
		if ($hours > 0 || $minutes > 0)
		{
			$time .= '<span>'.$minutes.'</span>:';
			$seconds = ($seconds < 10 ? '0'.$seconds : $seconds);
		}
		$time .= '<span>'.$seconds.'</span>';

		return '<span class="countdown" data-time="'.$date.'" data-now="'.time().'" data-callback="'.$callback.'">'.$time.'</span>';
	}
}

function getTimeUntil($date, $format = null)
{
        if (!is_numeric($date))
        {
                $date = strtotime($date);
        }

        if ($date == strtotime(''))
        {
                return 'never';
        }

        $now = time();

        $difference = $date-$now;

        $seconds = floor($difference);
        $minutes = floor($seconds/60);
        $hours = floor($minutes/60);
        $days = floor($hours/24);
        $weeks = floor($days/7);
        $months = floor($days/30.5);
        $years = floor($months/12);

        if ($seconds <= 1){
                return "<span class=\"time-until\">now</span>";
        } elseif ($minutes < 1 || $format == 's'){
                $seconds -= ($minutes * 60);
                $minutes = ($minutes < 10 ? '0'.$minutes : $minutes);
                $seconds = ($seconds < 10 ? '0'.$seconds : $seconds);
		if ($format == 's')
		{
                	return "in <span class=\"time-until\">{$minutes}:{$seconds}</span>";
		}
		else
		{
			return "in <span class=\"time-until\">a minute</span>";
                	#return "in <span class=\"time-until\">".ltrim($seconds, '0')."</span> seconds";
		}
        } elseif ($minutes == 1){
                return "in <span class=\"time-until\">one</span> minute";
        } elseif ($hours < 1 || $format == 'm'){
                return "in <span class=\"time-until\">$minutes</span> minutes";
        } elseif ($hours == 1){
                return "in <span class=\"time-until\">an hour</span>";
        } elseif ($days < 1 || $format == 'h'){
                return "in <span class=\"time-until\">$hours</span> hours";
        } elseif ($days == 1){
                return "<span class=\"time-until\">tomorrow</span>";
        } elseif ($weeks < 1){
                return "in <span class=\"time-until\">$days</span> days";
        } elseif ($weeks == 1){
                return "<span class=\"time-until\">next week</span>";
        } elseif ($months < 1){
                return "in <span class=\"time-until\">$weeks</span> weeks";
        } elseif ($months == 1){
                return "<span class=\"time-until\">next month</span>";
        } elseif ($months < 12){
                return "in <span class=\"time-until\">$months</span> months";
        } elseif ($years == 1){
                return "<span class=\"time-until\">next year</span>";
        } else {
                return "in <span class=\"time-until\">$years</span> years";
        }
}

function getTimeAgo($date)
{
        if (!is_numeric($date))
        {
                $date = strtotime($date);
        }

        if ($date == strtotime(''))
        {
                return 'never';
        }

        $now = time();

        $difference = $now-$date;

        $seconds = round($difference);
        $minutes = round($seconds/60);
        $hours = round($minutes/60);
        $days = round($hours/24);
        $weeks = round($days/7);
        $months = round($days/30.5);
        $years = round($months/12);

        if ($years > 1){
                return "<span class=\"time-until\">$years</span> years ago";
        } elseif ($years == 1){
                return "<span class=\"time-until\">a year</span> ago";
        } elseif ($months > 1){
                return "<span class=\"time-until\">$months</span> months ago";
        } elseif ($months == 1){
                return "<span class=\"time-until\">a month</span> ago";
        } elseif ($weeks > 1){
                return "<span class=\"time-until\">$weeks</span> weeks ago";
        } elseif ($weeks == 1){
                return "<span class=\"time-until\">a week</span> ago";
        } elseif ($days > 1){
                return "<span class=\"time-until\">$days</span> days ago";
        } elseif ($days == 1){
                return "<span class=\"time-until\">yesterday</span>";
        } elseif ($hours > 1){
                return "<span class=\"time-until\">$hours</span> hours ago";
        } elseif ($hours == 1){
                return "<span class=\"time-until\">an hour</span> ago";
        } elseif ($minutes > 1){
                return "<span class=\"time-until\">$minutes</span> minutes ago";
        } elseif ($minutes == 1){
                return "<span class=\"time-until\">a minute</span> ago";
        } elseif ($seconds > 1) {
                return "<span class=\"time-until\">$seconds</span> seconds ago";
        } else {
                return "<span class=\"time-until\">just now</span>";
        }
}

function is_date($date, $format = 'Y-m-d H:i:s')
{
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
}

function compactString($string)
{
        $new_string = false;
        while ($new_string !== $string){
                if ($new_string !== false){
                        $string = $new_string;
                }
                $new_string = trim(str_replace(array('  ', "\t", "\r\n", "\r", "\n", PHP_EOL), ' ', $string));
                $new_string = str_replace('; ', ';', $new_string);
                $new_string = str_replace(' = ', '=', $new_string);
                $new_string = str_replace('{ ', '{', $new_string);
                        //$new_string = str_replace(' }', '}', $new_string);
                $new_string = str_replace(', ', ',', $new_string);
        }
        return $new_string;
}

function backtrace($return = false)
{
        ob_start();
        echo debug(debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
	$trace = ob_get_contents();
        ob_end_clean();

	$trace = preg_replace('/^#0\s+' . __FUNCTION__ . "[^\n]*\n/", '', $trace, 1);

	if ($return)
	{
		return $trace;
	}
	else
	{
		echo $trace;
	}
}

function smarttime($date, $format = 'Y-m-d H:i:s')
{
        $new_date = str_replace('/', '-', $date);
        $parts = explode('-', $new_date);
        if (count($parts) == 3 && strlen($parts[2]) == 4) {
                $time = strtotime($parts[2].'-'.$parts[0].'-'.$parts[1]);
        } else {
                $time = strtotime($date);
        }
        if ($time && $format) {
                $time = date($format, $time);
        }
        return $time;
}

function email_valid($email)
{
        return true;
        return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function ans($string, $number)
{
	if ($number == 1)
	{
		return an($string, true);
	}
	else
	{
		return s($string, $number);
	}
}

function an($string, $include_string = true)
{
        $check = strip_tags($string);

        if (substr($check, -1) == 's')
        {
                return 'some'.($include_string ? ' '.$string : '');
        }

        switch (strtolower(substr($check, 0, 1))) {
                case 'a':
                case 'e':
                case 'i':
                case 'o':
                case 'u':
		case 'h':
                        return 'an'.($include_string ? ' '.$string : '');
                default:
                        return 'a'.($include_string ? ' '.$string : '');
        }
}

function s($string, $number = null)
{
        $original = strip_tags($string);

        if ($number === null)
        {
                if (substr($original, -1) == 's')
                {
                        $new = $original."'";
                }
                else
                {
                        $new = $original."'s";
                }

                $string = str_replace($original, $new, $string);

                return $string;
        }
        elseif (round($number) == 1)
        {
                return rtrim($string, 's');
        }
        else
        {
		switch (strtolower($original))
		{
			case 'gold':
			case 'energy':
			case 'wood':
			case 'metal':
				return $string;
		}

                if (substr($original, -2) == 'ey')
                {
                        $new = substr($original, 0, -2);
                        $new .= 'ies';
                }
                elseif (false && substr($original, -1) == 'y')
                {
                        $new = substr($original, 0, -1);
                        $new .= 'ies';
                }
		elseif (substr($original, -3) == 'ess')
		{
			$new = $original.'es';
		}
                elseif (substr($original, -1) != 's')
                {
                        $new = $original.'s';
                }
                else
                {
                        $new = $original;
                }

                $string = str_replace($original, $new, $string);

                return $string;
        }
}

function numeral($number, $html = false)
{
        if ($number == 0)
        {
                return '<i class="fa fa-ban"></i>';
        }

        $real = $number;

        $string = '';

        $numerals = [
                1000000000 => '<span><span>M</span></span>',
                900000000 => '<span><span>CM</span></span>',
                500000000 => '<span><span>D</span></span>',
                400000000 => '<span><span>CD</span></span>',
                100000000 => '<span><span>C</span></span>',
                90000000 => '<span><span>XC</span></span>',
                50000000 => '<span><span>L</span></span>',
                40000000 => '<span><span>XL</span></span>',
                10000000 => '<span><span>X</span></span>',
                9000000 => '<span><span>IX</span></span>',
                5000000 => '<span><span>V</span></span>',
                4000000 => '<span><span>IV</span></span>',
                1000000 => '<span>M</span>',
                900000 => '<span>CM</span>',
                500000 => '<span>D</span>',
                400000 => '<span>CD</span>',
                100000 => '<span>C</span>',
                90000 => '<span>XC</span>',
                50000 => '<span>L</span>',
                40000 => '<span>XL</span>',
                10000 => '<span>X</span>',
                9000 => '<span>IX</span>',
                5000 => '<span>V</span>',
                4000 => '<span>IV</span>',
                1000 => 'M',
                900 => 'CM',
                500 => 'D',
                400 => 'CD',
                100 => 'C',
                90 => 'XC',
                50 => 'L',
                40 => 'XL',
                10 => 'X',
                9 => 'IX',
                5 => 'V',
                4 => 'IV',
                1 => 'I',
        ];

        $round = strlen($real)-3;
        $digits = str_split($real);
        $places = count($digits);

        for ($i=1; $i<=$places; $i++)
        {
                $new_digits[] = ($i <= 3 ? array_shift($digits) : 0);
        }
        $number = implode($new_digits);

        $uppers = [];

        while ($number > 0)
        {
                foreach ($numerals as $int => $numeral)
                {
                        if ($number >= $int)
                        {
                                $number -= $int;
                                $string .= $numeral;
                                break;
                        }
                }
        }

        return ($html ? '<span class="tooltip"><span class="numeral">'.$string.'</span><div>'.number_format($real).'</div></span>' : $string);
}

function number($number)
{
        switch ($number) {
                case 0:
                        return 'no';
                case 1:
                        return 'one';
                case 2:
                        return 'two';
                case 3:
                        return 'three';
                case 4:
                        return 'four';
                case 5:
                        return 'five';
                case 6:
                        return 'six';
                case 7:
                        return 'seven';
                case 8:
                        return 'eight';
                case 9:
                        return 9;
                default:
                        return number_format($number);
        }
}

function big_number_format($number, $places = 0)
{
        $sizes = [
                1000000000000 => 'T',
                1000000000 => 'B',
                1000000 => 'M',
                1000 => 'K',
        ];

        foreach ($sizes as $min => $label)
        {
                if ($number >= $min)
                {
                        return number_format($number / $min, $places).$label;
                }
        }

        return $number;
}

function size($bytes, $format = null, $decimals = 2, $number_format = true)
{
        if ($format == null)
        {
                if ($bytes > tb())
                {
                        $format = 't';
                }
                elseif ($bytes > gb())
                {
                        $format = 'g';
                }
                elseif ($bytes > mb())
                {
                        $format = 'm';
                }
                elseif ($bytes > kb())
                {
                        $format = 'k';
                }
                else
                {
                        $format = '';
                }
        }

        switch (strtolower($format))
        {
                case '':
                        $value = $bytes;
                        break;
                case 'k':
                        $value = $bytes/kb();
                        break;
                default:
                case 'm':
                        $value = $bytes/mb();
                        break;
                case 'g':
                        $value = $bytes/gb();
                        break;
                case 't':
                        $value = $bytes/tb();
                        break;
        }

        if ($number_format)
        {
                $value = number_format($value, $decimals);
        }
        elseif ($decimals)
        {
                $value = round($value, $decimals);
        }

        $value .= ' '.strtoupper($format.'B');

        return $value;
}

function kb()
{
        return 1024;
}

function mb()
{
        return 1048576;
}

function gb()
{
        return 1073741824;
}

function tb()
{
        return 1099511627776;
}

function prettySize($bytes, $digits = 2)
{
        $sizes = ['b'=>'B', 'k'=>'KB', 'm'=>'MB', 'g'=>'GB', 't'=>'TB'];
        foreach ($sizes as $size => $label) {
                $size_formatted = size($bytes, $size, 0, false);
                if ($size_formatted < 1000) {
                        return number_format($size_formatted, $digits).' '.$label;
                }
        }
}

function toFunction($string)
{
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
}

function spaces($string)
{
        $string = str_replace("\r", "<br />", $string);
        $string = str_replace("  ", "&nbsp;&nbsp;", $string);
        return $string;
}

function line_exec($cmd)
{
        return explode(PHP_EOL, trim(shell_exec($cmd)));
}

function imRunning($times = 0, $process = null)
{
        if (!$process) {
                $process = basename($_SERVER['PHP_SELF']);
        }
        $running = line_exec("ps axf | grep '$process' | grep -v grep | grep -v '/bin/sh'");
        $running_times = count($running);
        if ($times == 0 && $running_times == 1) {
                return false;
        } elseif ($times == 0 && $running_times > 1) {
                return true;
        } else {
                return !($running_times < $times);
        }
}

function numberToWord($number)
{
        $number = preg_replace('/[^0-9]/', '', $number);
        $length = strlen($number);

        $tens = ['ten','twenty','thirty','fourty','fifty','sixty','seventy','eighty','ninety'];
        $thousands = ['hundred','thousand','million','billion','trillion'];
        $small = ['','one','two','three','four','five','six','seven','eight','nine','ten',
                'eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];

        if ($number == 0) {
                return 'zero';
        } elseif ($number < 0) {
                return '';
        } elseif (!empty($small[$number])) {
                return $small[$number];
        } elseif ($number <= 99) {
                $first = substr($number, 0, 1);
                $last = substr($number, -1);
                return $tens[$first].'-'.$small[$last];
        }

        $big = '';
        $word = '';

        if ($length >= 13) {
                $big = substr($number, 0, -12);
                $word = 'trillion';
                $number -= $big*1000000000000;
        } elseif ($length >= 10) {
                $big = substr($number, 0, -9);
                $word = 'billion';
                $number -= $big*1000000000;
        } elseif ($length >= 7) {
                $big = substr($number, 0, -6);
                $word = 'million';
                $number -= $big*1000000;
        } elseif ($length >= 4) {
                $big = substr($number, 0, -3);
                $word = 'thousand';
                $number -= $big*1000;
        } elseif ($length >= 3) {
                $big = substr($number, 0, -2);
                $word = 'hundred';
                $number -= $big*100;
        } else {
                return $number;
        }

        if ($number) {
                return numberToWord($big).' '.$word.' '.numberToWord($number);
        } else {
                return numberToWord($big).' '.$word;
        }
}

function st($number)
{
        switch ($number)
        {
		case 1: return 'first';
		case 2: return 'second';
		case 3: return 'third';
		case 4: return 'fourth';
		case 5: return 'fifth';
		case 6: return 'sixth';
		case 7: return 'seventh';
		case 8: return 'eighth';
		case 9: return 'nineth';
		case 10: return 'tenth';
                case 11: return 'eleventh';
                case 12: return 'twelvth';
                case 13: return 'thirteenth';

                default:
                        $last = substr($number, -1);
                        switch ($last)
                        {
                                case 1:
                                        return $number.'st';
                                case 2:
                                        return $number.'nd';
                                case 3:
                                        return $number.'rd';
                                default:
                                        return $number.'th';
                        }
                        break;
        }
}

function only($number)
{
        if ($number == 0)
        {
                return 'none';
        }
        else
        {
                return 'only '.number_format($number);
        }
}

function commas($values)
{
        $last = array_pop($values);
        if ($values)
        {
                $last = 'and '.$last;
        }
        $values[] = $last;
        if (count($values) > 2)
        {
                $values = implode(', ', $values);
        }
        else
        {
                $values = implode(' ', $values);
        }
        return $values;
}

function numberFormat($number, $places = 0)
{
        $number = number_format($number, $places);

        if ($places > 0)
        {
                $number = explode('.', $number);
                $ints = array_shift($number);
                $decimals = array_pop($number);
                $number = '<b>'.$ints.'</b><small>.'.$decimals.'</small>';
        }

        return $number;
}

function strcontains($string, $contains)
{
        $string = strtolower($string);
        $contains = strtolower($contains);
        $contains = str_replace('%', '(\S+)', $contains);

        return preg_match('/'.$contains.'/', $string, $matches);
}

function parseDataCsv($string)
{
        $data = [];
        $lines = explode(',', $string);
        foreach ($lines as $line)
        {
                $line = explode('=', $line);
                $key = trim(array_shift($line));
                $value = trim(array_pop($line));
                $data[$key] = $value;
        }
        return $data;
}

/**
 * randomize stuff
 * if min and max are ints:
 *    generate a number to $precision decimals
 *    if $precision not set, will use most decimal places from min and max
 * if min is an array, randomize it
 */
function random($min = null, $max = null, $precision = null)
{
        if (is_array($min))
        {
                for ($i=1; $i<=rand(1,count($min)); $i++)
                {
                        shuffle($min);
                }

                return array_shift($min);
        }
        elseif (is_numeric($min))
        {
                if (!is_numeric($max))
                {
                        $max = floor($min+1);
                }

                $multiplier = 1;

                if ($precision === null)
                {
                        $min_decimal = '';
                        if ((int)$min != $min)
                        {
                                $min_decimal = explode('.', $min);
                                $min_decimal = array_pop($min_decimal);
                        }
                        $max_decimal = '';
                        if ((int)$max != $max)
                        {
                                $max_decimal = explode('.', $max);
                                $max_decimal = array_pop($max_decimal);
                        }

                        $precision = max(strlen($min_decimal), strlen($max_decimal));
                }

                for ($i=1; $i<=$precision; $i++)
                {
                        $multiplier = $multiplier * 10;
                        
                        $min = $min * 10;
                        $max = $max * 10;
                }

                $value = rand($min, $max);
                
                $value = $value / $multiplier;

                return $value;
        }
        else
        {
                return random([$min, $max]);
        }
}

/**
 * add commas to large number
 * do not round or do anything with decimals
 */
function add_commas($number)
{
        $parts = explode('.', $number);
        $parts[0] = number_format($number);
        $number = implode('.', $parts);
        return $number;
}

/**
 * round a number to x decimals
 * if number is less, use "< 0.xx"
 */
function roundOrLess($number, $places)
{
        $int = pow(10, $places);
        $min = 1 / $int;

        if ($number < $min)
        {
                $number = '< '.$min;
        }
        else
        {
                $number = round($number, $places);
                $number = number_format($number, $places);
        }

        return $number;
}

/**
 * round a number to a max of X decimals
 */
function smartRound($number, $places = null)
{
	if (!is_numeric($number))
	{
		return $number;
	}

	if ($places !== null)
	{
		$number = round($number, $places);
	}

	$number = rtrim($number, '0');
	$number = rtrim($number, '.');

	return $number;
}

/**
 * get all lines in a string
 */
function getLines($string, $allow_empty = false)
{
        $string = trim($string);
        $string = str_replace(["\r\n",PHP_EOL,"<br>","<br />",";"], "\r\n", $string);

        if (!$string)
        {
                return [];
        }

        $string = explode("\r\n", $string);
        foreach ($string as $i => $line)
        {
                $string[$i] = trim($line);
                if (!$allow_empty && empty($string[$i]))
                {
                        unset($string[$i]);
                }
        }

        return $string;
}

/**
 * set a value in cache
 */
function cache($name, $set = null)
{
        $file = '/tmp/cache/'.$name.'.tmp';

        if (!is_dir('/tmp/cache'))
        {
                mkdir('/tmp/cache');
        }

        if ($set)
        {
                $GLOBALS['cache'][$file] = $set;
                $set = var_export($set, true);
                $tmp = '/tmp/cache/'.$name.uniqid('', true).'.tmp';
                file_put_contents($tmp, '<?php $val = '.$set.';', LOCK_EX);
                rename($tmp, $file);
                return file_exists($tmp);
        }
        elseif (isset($GLOBALS['cache'][$file]))
        {
                return $GLOBALS['cache'][$file];
        }
        else
        {
                if (file_exists($file))
                {
                        include $file;
                        $GLOBALS['cache'][$file] = $val;
                        return $val;
                }
        }
}
