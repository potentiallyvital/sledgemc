<?php

// input handler functions

function title($string)
{
        $words = explode(' ', $string);

        $html = [];
        $html[] = '<span class="title">';
        foreach ($words as $word)
        {
                if (strlen($word) >= 4)
                {
                        $letters = str_split($word);
                        $first = array_shift($letters);
                        $last = implode('', $letters);

                        $html[] = '<span class="first">'.$first.'</span>'.$last;
                }
                else
                {
                        $html[] = $word;
                }
        }
        $html[] = '</span>';

        return implode('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $html);
}

function post($variable = null, $validate = false)
{
        if (!$variable)
        {
                $values = [];
                foreach ($_POST as $variable => $value)
                {
                        $values[$variable] = post($variable, $validate);
                }
                foreach ($_GET as $variable => $value)
                {
                        $values[$variable] = post($variable, $validate);
                }
                foreach ($_FILES as $variable => $value)
                {
                        $values[$variable] = post($variable, $validate);
                }

                return $values;
        }

        $value = false;
        if (isset($_POST[$variable]))
        {
                $value = $_POST[$variable];
        }
        elseif (isset($_FILES[$variable]))
        {
                $names = $_FILES[$variable]['name'];
                $paths = $_FILES[$variable]['tmp_name'];
                $sizes = $_FILES[$variable]['size'];
                $types = $_FILES[$variable]['type'];

                foreach ($names as $i => $name)
                {
                        $data = [];
                        $data['name'] = $name;
                        $data['path'] = $paths[$i];
                        $data['type'] = $types[$i];
                        $data['size'] = $sizes[$i];

                        $value[$name] = $data;
                }
        }
        elseif (isset($_GET[$variable]))
        {
                $value = $_GET[$variable];
        }
        elseif (isset($_SESSION[$variable]))
        {
                $value = $_SESSION[$variable];
        }

        if ($validate)
        {
                $options = ['~','|','/','(',')','-','_','+','='];
                foreach ($options as $option)
                {
                        if (!stristr($validate, $option))
                        {
                                return preg_replace($option.'[^'.$validate.']'.$option, '', $value);
                        }
                }
        }

        return $value;
}

function hidden($name, $value, $data = [])
{
        $data['type'] = 'hidden';
        $data['value'] = $value;
        $data = normalize_for_input($name, $data);

        return "<input {$data['vars']} />";
}

function numeric($name, $data = [])
{
        $data['type'] = 'number';
        if (!isset($data['value']))
        {
                $data['value'] = 0;
        }

        return textbox($name, $data);
}

function textbox($name, $data = [])
{
        if (stristr($name, 'password') && empty($data['type']))
        {
                $data['type'] = 'password';
                $data['value'] = '';
                $_POST[$name] = '';
        }
        elseif (stristr($name, 'email') && empty($data['type']))
        {
                $data['type'] = 'email';
        }
	elseif (!empty($data['type']) && $data['type'] == 'date')
	{
		$data['type'] = 'text';
		$data['class'] = (!empty($data['class']) ? $data['class'].' date' : 'date');
		$data['placeholder'] = 'mm/dd/yyyy';
		if (!empty($data['value']))
		{
			$data['value'] = date('m/d/Y', strtotime($data['value']));
		}
	}

        $data['type'] = (!empty($data['type']) ? $data['type'] : 'text');
        $data['autocomplete'] = 'off';
        $data = normalize_for_input($name, $data);

        $html = '<div class="textbox">';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].':</label>';
        }
        $html .= '<input '.$data['vars'].' />';
        $html .= '</div>';

        return $html;
}

function textarea($name, $data = [])
{
        $value = '';
        if (empty($data['novalue']))
        {
                $value = (post($name) ?: (!empty($data['value']) ? $data['value'] : ''));
        }

        unset($data['value']);
        $data = normalize_for_input($name, $data);

        $html = '<div class="textarea">';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].':</label>';
        }
        $html .= '<textarea '.$data['vars'].'>'.$value.'</textarea>';
        $html .= '</div>';

        return $html;
}

function autofill($name, $data = [])
{
        $data['special'] = 'autofill';

        return dropdown($name, $data);
}

function lookup($name, $data = [])
{
        $data['options'] = [];
        $data['special'] = 'lookup';
        $data['data-what'] = $name;

        return dropdown($name, $data);
}

function dropdown($name, $data = [])
{
        $special = (!empty($data['special']) ? $data['special'] : '');
        unset($data['special']);

        $value = (!empty($data['value']) ? $data['value'] : '');
        $data = normalize_for_input($name, $data);
        $selected_value = (post($name) ?: $value);
        $options = '';
        $show_value = '';
        foreach ($data['options'] as $value => $label)
        {
                if (is_object($label))
                {
                        $value = $label->id;
                        $label = $label->name;
                }

                $options .= '<div class="dropdown-option '.$special.'" data-value="'.$value.'">'.$label.'</div>';
                if ($value == $selected_value)
                {
                        $show_value = $label;
                }
        }

        $html = '';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].':</label>';
        }
        $html .= '<div class="dropdown-wrapper '.$special.'">';
        $html .= '<input type="text" class="input pointer dropdown '.$special.'" value="'.$show_value.'" />';
        $html .= '<input type="hidden" '.$data['vars'].' />';
        $html .= '<div class="dropdown-options input '.$special.'">';
        $html .= $options;
        $html .= '</div>';
        $html .= '</div>';

        return $html;
}

function button($name, $data = [], $url = null)
{
        $data['value'] = (!empty($data['value']) ? $data['value'] : 1);
        $data['class'] = (!empty($data['class']) ? $data['class'].' button' : 'button');
        $data = normalize_for_input($name, $data);

        $html = '<div class="button-wrapper">';
        $html .= '<button '.$data['vars'].'>'.$data['label'].'</button>';
        if ($url)
        {
                $html = '<a href="'.$url.'">'.$html.'</a>';
        }
        $html .= '</div>';

        return $html;
}

function checkall()
{
        return button('check-all', ['label'=>'Check All','class'=>'float-left']);
}

function checkbox($name, $data = [])
{
        $data['type'] = 'checkbox';
        $data['value'] = (!empty($data['value']) ? $data['value'] : 1);

        if (!empty($data['checked']))
        {
                $data['checked'] = "checked='true'";
        }
        else
        {
                $name_parts = explode('[', $name);
                $name_array = array_shift($name_parts);
                $posted = post($name_array);
                if (is_array($posted))
                {
                        $id = array_pop($name_parts);
                        $id = substr($id, 0, -1);
                        if (!empty($posted[$id]))
                        {
                                $data['checked'] = "checked='true'";
                        }
                }
                elseif (post($name) == $data['value'])
                {
                        $data['checked'] = "checked='true'";
                }
        }

        $data = normalize_for_input($name, $data);

        $html = '<div class="checkbox">';
        $html .= "<input {$data['vars']} />";
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="checkbox-label">'.$data['label'].'</label>';
        }
        $html .= '</div>';

        return $html;
}

function checkboxes($name, $data = [])
{
        $data['type'] = 'checkbox';

        $data = normalize_for_input($name, $data);

        $html = '<div class="checkboxes">';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].'</label>';
        }

        $html .= '<table class="no-break">';
        foreach ($data['options'] as $value => $label)
        {
                $id = $data['id'].'-'.$value;

                $checkbox_data = $data;
                $checkbox_data['id'] = $id;
                $checkbox_data['value'] = $value;
                if (!empty($checkbox_data['checked']) || post($name) === $value)
                {
                        $checkbox_data['checked'] = 'true';
                }
                else
                {
                        unset($checkbox_data['checked']);
                }

                $checkbox_data = normalize_for_input($name, $checkbox_data);
                $html .= '<tr><td class="middle"><input '.$checkbox_data['vars'].' /></td><td class="middle"><label for="'.$id.'" class="checkbox-label">'.$label.'</label></td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';

        return $html;
}

function toggle($name, $data = [])
{
        $data['options'] = ['Off','On'];

        return radios($name, $data);
}

function radios($name, $data = [])
{
        $data['type'] = 'radio';

        $data = normalize_for_input($name, $data);

        $html = '<div class="radios">';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].'</label>';
        }

        foreach ($data['options'] as $value => $label)
        {
                if (empty($GLOBALS['radio_i']))
                {
                        $GLOBALS['radio_i'] = 0;
                }
                $id = $data['id'].'-'.$value.'-'.$GLOBALS['radio_i']++;

                $radio_data = $data;
                $radio_data['id'] = $id;
                $radio_data['value'] = $value;
                if (!empty($radio_data['checked']) || post($name) === $value || (!post($name) && $value == $data['value']))
                {
                        $radio_data['checked'] = 'true';
                }
                else
                {
                        unset($radio_data['checked']);
                }
                $radio_data = normalize_for_input($name, $radio_data);

                $html .= '<div class="radio">';
                $html .= '<input '.$radio_data['vars'].' />';
                $html .= '<label for="'.$id.'" class="radio-label">'.$label.'</label>';
                $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
}

function upload($name, $data = [], $multiple = false)
{
        $data['type'] = 'file';
        $multiple = ($multiple ? ' multiple' : '');
        $data = normalize_for_input($name, $data);

        $html = '<div class="textbox">';
        if (empty($data['nolabel']))
        {
                $html .= '<label for="'.$data['id'].'" class="textbox-label">'.$data['label'].':</label>';
        }
        $html .= '<input '.$data['vars'].$multiple.' />';
        $html .= '</div>';
        return $html;
}

function normalize_for_input($name, $data = [])
{
        // assign a label if not set already
        if (empty($data['label']))
        {
                $data['label'] = ucwords(deslugify(slugify($name)));
        }

        // give it a good name
        $data['name'] = (isset($data['name']) ? $data['name'] : preg_replace('/[^-[]_a-zA-Z0-9]/', '', $name));

        // every input needs an id
        if (empty($data['id']))
        {
                $data['id'] = str_replace('_', '-', $data['name']);
        }

        // populate with pre-submitted form values
        // if not already submitted, use default value if specified
        // do not give all radios this value since they have the same name
        if (empty($data['novalue']))
        {
                if (post($name) && (empty($data['type']) || $data['type'] != 'radio'))
                {
                        $data['value'] = post($name);
                }
                elseif (!isset($data['value']))
                {
                        $data['value'] = '';
                }
        }

        // give all inputs the input css class
        if (!isset($data['class']))
        {
                $data['class'] = 'input';
        }
        else
        {
                $data['class'] = 'input '.$data['class'];
        }

        // append the name to the class
        $data['class'] .= ' '.slugify($data['name']).'';

        // clear up classes
        $unique_classes = [];
        $classes = explode(' ', $data['class']);
        foreach ($classes as $class)
        {
                $unique_classes[$class] = $class;
        }
        $data['class'] = implode(' ', $unique_classes);

        // add onclick js event if url is specified
        if (isset($data['url']))
        {
                if (empty($data['onclick']))
                {
                        $data['onclick'] = '';
                }
                $data['onclick'] .= 'window.location.href=BASE_URL+"'.$data['url'].'";';
                unset($data['url']);
        }

        // build all variables for the element
        unset($data['vars']);
        $vars = [];
        foreach ($data as $html_key => $html_value)
        {
                if (is_string($html_value) || is_numeric($html_value))
                {
                        $vars[] = $html_key.'="'.trim(strip_tags($html_value)).'"';
                }
        }
        $vars = implode(' ', $vars);
        $data['vars'] = $vars;

        return $data;
}

function color($name, $data = [])
{
        if (post($name))
        {
                $rgb = post($name);
        }
        elseif (!empty($data['value']))
        {
                $rgb = $data['value'];
        }
        else
        {
                $rgb = 'transparent';
        }
        return '
                <div class="color" style="background-color:'.$rgb.';">
                        <input type="hidden" name="'.$name.'" id="'.$name.'" class="'.str_replace('_', '-', $name).'" value="'.$rgb.'" />
                </div>
        ';
}
