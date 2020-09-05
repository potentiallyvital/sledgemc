<?php

function dash()
{
        return '<span class="dash">&mdash;</span>';
}

function prettyText($string)
{
        $string = str_replace([PHP_EOL,"\r\n"], '<br />', $string);

        $html = '';
        $html .= '<div class="pre">';
        $html .= $string;
        $html .= '</div>';

        return $html;
}

function nestedList($array, $first = true)
{
        $html = '';

        if ($first)
        {
                $html .= '<ul>';
        }
        foreach ($array as $heading => $children)
        {
                if ($children)
                {
                        if (is_array($children))
                        {
                                if ($heading)
                                {
                                        $html .= '<li>'.$heading.'</li>';
                                }
                                $html .= nestedList($children);
                        }
                        elseif (is_string($children))
                        {
                                $html .= '<li>'.$children.'</li>';
                        }
                        elseif (is_object($children))
                        {
                                $children = get_object_vars($children);
                                $html .= nestedList($children, false);
                        }
                }
        }
        if ($first)
        {
                $html .= '</ul>';
        }

        return $html;
}
