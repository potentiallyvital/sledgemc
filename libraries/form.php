<?php
/****
 * form helper functions
 *
 * @author : Vital (potentiallyvital@gmail.com)
 */
?>
<?php

defined('FORM_LABEL_COL_WIDTH') || define('FORM_LABEL_COL_WIDTH', 4);

/**
 * add a tooltip to something
 */
function tooltip($tip, $html)
{
	return '<span class="tooltip"><div>'.$tip.'</div>'.$html.'</span>';
}

/**
 * add an error to a field
 */
function error($name, $message)
{
	$GLOBALS['errors'][$name][] = $message;
}

/**
 * check if there are errors (general or field specific)
 */
function errors($name = null)
{
	if (empty($name))
	{
		return !empty($GLOBALS['errors']);
	}

	if (empty($GLOBALS['errors'][$name]))
	{
		return false;
	}

	return '<div class="input input-errors"><b>&times;</b> '.implode('<br />', $GLOBALS['errors'][$name]).'</div>';
}

/**
 * show a hidden field
 */
function hidden($name, $value)
{
	$value = (post($name) ?: ($value !== null ? $value : ''));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' hidden';

	return '<input type="hidden" class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" />';
}

/**
 * show some text indented like a form element
 */
function label($label, $options = [])
{
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	return '<div class="input-wrapper form-label"><div class="row"><div class="col-md-'.$label_width.'"></div><div class="col-md-'.$value_width.'">'.$label.'</div></div></div>';
}

/**
 * show a color field
 */
function color($name, $options = [])
{
$options['class'] = (isset($options['class']) ? $options['class'] : '').' color';
return textbox($name, $options);

	$value = (post($name) ?: (isset($options['value']) ? $options['value'] : ''));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input color';
	if (!$label)
	{
		$options['width'] = 0;
	}
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

/*
	if (isset($options['transparency']))
	{
		if ($options['transparency'] === true)
		{
			if (substr($value, 0, 3) == 'rgb')
			{
				$values = str_replace(['rgb(','rgba(',')'], '', $value);
				$values = explode(',', $values);

				$r = array_shift($values);
				$g = array_shift($values);
				$b = array_shift($values);
				$a = array_shift($values);
				$a = ($a === null ? 1 : $a);
				$transparency = $a*100;
				$a = $a*255;

				$r = str_pad(dechex((int)$r), 2, '0', STR_PAD_LEFT);
				$g = str_pad(dechex((int)$g), 2, '0', STR_PAD_LEFT);
				$b = str_pad(dechex((int)$b), 2, '0', STR_PAD_LEFT);
				$a = str_pad(dechex((int)$a), 2, '0', STR_PAD_LEFT);

				$value = '#'.$r.$g.$b.$a;
			}
			if (substr($value, 0, 1) == '#' && strlen($value) == 9)
			{
				$transparency = round(hexdec(substr($value, -2))/255*100);
			}
			else
			{
				$transparency = 100;
			}
		}
		else
		{
			$transparency = $options['transparency'];
		}
	}
*/

	$autocomplete = (isset($options['autocomplete']) ? $options['autocomplete'] : false);
	if ($autocomplete)
	{
		$class .= ' autocomplete';
	}

	$disabled = '';
	if (isset($options['disabled']))
	{
		$disabled = 'disabled="'.$options['disabled'].'"';
		if ($options['disabled'])
		{
			$class .= ' disabled';
		}
	}

	$html = [];
	$html[] = '<div class="input-wrapper">';
	$html[] = '<i class="fa fa-brush color-picker"></i>';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<input type="text" class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" style="background-color:'.$value.'" '.$disabled.' />';
	#$html[] = '<input type="color" class="hidden" value="'.$value.'" />';
	if ($label)
	{
		$html[] = '</div></div>';
	}

/*
	if ($transparency !== null)
	{
		$options = [0=>'Transparent',10=>'10%',20=>'20%',30=>'30%',40=>'40%',50=>'50%',60=>'60%',70=>'70%',80=>'80%',90=>'90%',100=>'Opaque'];
		$html[] = dropdown($id.'-transparency', ['label'=>'Transparency','value'=>$transparency,'options'=>$options,'width'=>8,'class'=>'transparency']);
	}
*/

	$html[] = '</div>';

	return implode('', $html);
}

/**
 * show a textbox
 */
function textbox($name, $options = [])
{
	$value = (post($name) ?: (isset($options['value']) ? $options['value'] : ''));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$type = (isset($options['type']) ? $options['type'] : 'text');
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input';
	$color = (stristr($class, 'color'));
	$style = (isset($options['style']) ? 'style="'.$options['style'].'"' : ($color ? 'style="background-color:'.$value.';border-color:'.$value.'"' : ''));
	$placeholder = (isset($options['placeholder']) ? 'placeholder="'.$options['placeholder'].'"' : '');

	if (!empty($options['tooltip']))
	{
		$label = tooltip($options['tooltip'], $label.' <i class="fa fa-circle-question text-purple"></i>');
	}

	if (!$label)
	{
		$options['width'] = 0;
	}
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	$autocomplete = (isset($options['autocomplete']) ? $options['autocomplete'] : false);
	if ($autocomplete)
	{
		$class .= ' autocomplete';
	}

	$disabled = '';
	if (isset($options['disabled']))
	{
		$disabled = 'disabled="'.$options['disabled'].'"';
		if ($options['disabled'])
		{
			$class .= ' disabled';
		}
	}

	$html = [];
	$html[] = '<div class="input-wrapper">';
	if ($color)
	{
		$html[] = '<i class="input-icon fa fa-brush color-picker left"></i>';
		$html[] = '<i class="input-icon fa fa-trash remove-color" '.($value ? '' : 'style="display:none;"').'></i>';
	}
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<input type="'.$type.'" class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$style.' '.$disabled.' '.$placeholder.' />';
	if ($label)
	{
		$html[] = '</div></div>';
	}
	$html[] = '</div>';

	return implode('', $html);
}

/**
 * show a checkbox
 */
function checkbox($name, $options = [])
{
	$options['class'] = (isset($options['class']) ? $options['class'] : '').' checkbox';
	$options['options'] = ['Yes'=>(!empty($options['checked'])),'No'=>(empty($options['checked']))];
	$options['checkbox'] = true;

	return toggle($name, $options);

/*
	$value = (isset($options['value']) ? $options['value'] : '1');
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input';
	$style = (isset($options['style']) ? 'style="'.$options['style'].'"' : '');
	$checked = (!empty($options['checked']) ? 'checked="true"' : '');
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	$html = [];
	$html[] = '<div class="input-wrapper">';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<input type="checkbox" class="'.$class.'" id="'.$id.'" name="'.$name.'" '.$style.' value="'.$value.'" '.$checked.' />';
	if ($label)
	{
		$html[] = '</div></div>';
	}
	$html[] = '</div>';

	return implode('', $html);
*/
}

/**
 * show a textarea
 */
function textarea($name, $options = [])
{
	$value = (post($name) ?: (isset($options['value']) ? $options['value'] : ''));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input';
	$style = (isset($options['style']) ? 'style="'.$options['style'].'"' : '');
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	$html = [];
	$html[] = '<div class="input-wrapper">';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<textarea class="'.$class.'" id="'.$id.'" name="'.$name.'" '.$style.'>'.$value.'</textarea>';
	if ($label)
	{
		$html[] = '</div></div>';
	}
	$html[] = '</div>';

	return implode('', $html);
}

/**
 * show a fancy dropdown
 */
function dropdown($name, $options = [])
{
	$value = (post($name) ?: (isset($options['value']) ? $options['value'] : ''));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input dropdown';
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	$empty_text = 'No matches found';
	if (!empty($options['freeform']))
	{
		$class .= ' freeform';
		$empty_text = $options['freeform'];
		if (empty($options['options']))
		{
			$options['options'] = [];
		}
	}
	elseif (empty($options['options']))
	{
		return;
	}

	$show_value = '';
	if (isset($options['options'][$value]))
	{
		$show_value = $options['options'][$value];
	}

	$html = [];
	$html[] = '<div class="input-wrapper">';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<div class="dropdown-wrapper">';
	if (!empty($options['freeform']))
	{
		$html[] = '<input type="text" id="'.$id.'" name="dropdown['.$name.'][visible]" class="'.$class.'" value="'.$show_value.'" />';
		$html[] = '<input type="hidden" name="dropdown['.$name.'][hidden]" value="'.$value.'" />';
	}
	else
	{
		$html[] = '<input type="text" id="'.$id.'" name="'.$name.'" class="'.$class.'" value="'.$show_value.'" />';
		$html[] = '<input type="hidden" id="'.$id.'-key" name="'.$name.'_key" value="'.$value.'" />';
	}
	$html[] = '<div class="dropdown-options input">';
	foreach ($options['options'] as $option_id => $option_label)
	{
		$option_class = 'dropdown-option '.($option_label == $show_value ? 'selected' : '');
		$html[] = '<div class="'.$option_class.'" data-value="'.$option_id.'">'.$option_label.'</div>';
	}
	if (is_string($empty_text))
	{
		$html[] = '<div class="dropdown-option selected empty">'.$empty_text.'</div>';
	}
	$html[] = '</div>';
	$html[] = '</div>';
	if ($label)
	{
		$html[] = '</div></div>';
	}
	$html[] = '</div>';

	return implode('', $html);

}

/**
 * show an upload form
 */
function upload($name, $options = [])
{
	$value = (isset($options['value']) ? $options['value'] : '');
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input';
	$img = (isset($options['img']) ? $options['img'] : false);
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	if ($img)
	{
		$value_width -= 4;
	}

	$html = [];
	$html[] = '<div class="input-wrapper">';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	$html[] = errors($name);
	$html[] = '<input type="file" class="'.$class.'" id="'.$id.'" name="'.$name.'">';
	if ($label)
	{
		$html[] = '</div>';
	}
	if ($img)
	{
		if ($label)
		{
			$html[] = '<div class="col-md-4">';
		}
		if ($value)
		{
			$html[] = '<img class="preview-img" src="'.$value.'" />';
		}
		else
		{
			$html[] = '<img class="preview-img hidden" />';
		}
		if ($label)
		{
			$html[] = '</div>';
		}
	}
	$html[] = '</div>';
	$html[] = '</div>';

	return implode('', $html);
}

/**
 * show a button
 */
function button($name, $options = [])
{
	$value = (isset($options['value']) ? $options['value'] : post($name));
	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input button submit';
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	if (!empty($options['icon']))
	{
		$label .= ' <i class="fa '.$options['icon'].' fa-right"></i>';
	}

	$html = [];
	$html[] = '<div class="input-wrapper">';
	$html[] = '<div class="row">';

	if ($label_width)
	{
		$html[] = '<div class="col-md-'.$label_width.'"></div>';
		$html[] = '<div class="col-md-'.$value_width.'">';
			$html[] = '<button class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$value.'">'.$label.'</a>';
		$html[] = '</div>';
	}
	else
	{
		$html[] = '<div class="col-12 text-right">';
			$html[] = '<button class="'.$class.'" id="'.$id.'" name="'.$name.'" value="'.$value.'">'.$label.'</a>';
		$html[] = '</div>';
	}

	$html[] = '</div>';
	$html[] = '</div>';

	return implode('', $html);
}

/**
 * toggle buttons
 */
function toggle($name, $options = [])
{
	$options['class'] = (isset($options['class']) ? $options['class'] : '').' toggle';
	$options['toggle'] = true;

	return buttons($name, $options);
}

/**
 * show a list of toggleable buttons
 */
function buttons($name, $options = [])
{
	$values = $options['options'];
	if (empty($values))
	{
		return;
	}

	$id = (isset($options['id']) ? $options['id'] : slugify($name));
	$label = (isset($options['label']) ? $options['label'] : deslugify($name));
	$class = (isset($options['class']) ? $options['class'] : '').' '.$id.' input button';
	$toggle = (!empty($options['toggle']));
	$label_width = (isset($options['width']) ? $options['width'] : FORM_LABEL_COL_WIDTH);
	$value_width = 12-$label_width;

	$html = [];
	$html[] = '<div class="input-wrapper buttons">';
	if ($label)
	{
		$html[] = '<div class="row"><div class="col-md-'.$label_width.' text-right-md-up"><label for="'.$id.'">'.$label.'</label></div><div class="col-md-'.$value_width.'">';
	}
	foreach ($values as $option_name => $checked)
	{
		$disabled = ($checked === 'DISABLED');

		if (post($name))
		{
			$value = (!empty($_POST[$name][$option_name]) || (!empty($_POST[$name]) && $_POST[$name] == $option_name) ? 1 : 0);
		}
		else
		{
			$value = ($checked ? 1 : 0);
		}

		$button_class = ($value ? $class : $class.' grey');

		if ($value && $toggle)
		{
			if (in_array($option_name, ['On','Yes']))
			{
				$button_class .= ' green';
			}
			elseif (in_array($option_name, ['Off','No']))
			{
				$button_class .= ' red';
			}
		}
		if ($disabled)
		{
			$button_class .= ' disabled';
		}

		$html[] = ' <span class="button-wrapper">';
		if (!$disabled)
		{
			$selected = ($value ? 'checked="true"' : '');

			if (empty($options['toggle']))
			{
				$html[] = '<input id="'.$id.'_'.$option_name.'" type="checkbox" class="hidden '.$id.'" name="'.$name.'[]" value="'.$option_name.'" '.$selected.' />';
			}
			else
			{
				$html[] = '<input id="'.$id.'_'.$option_name.'" type="radio" class="hidden '.$id.'" name="'.$name.'" value="'.$option_name.'" '.$selected.' />';
			}
		}

		if (!empty($options['checkbox']))
		{
			if ($option_name == 'Yes')
			{
				$option_name = '<i class="fa fa-check"></i>';
			}
			elseif ($option_name == 'No')
			{
				$option_name = '<i class="fa fa-ban"></i>';
			}
		}

		$html[] = '<a class="'.$button_class.'">'.$option_name.'</a>';
		$html[] = '</span>';
	}
	if ($label)
	{
		$html[] = '</div></div>';
	}
	$html[] = '</div>';

	return implode('', $html);
}

/**
 * retrieve post/get/session/cookie fields in that order
 * no more "if (!empty($_POST['somename']))" ugliness
 */
function post($name = null, $format = '')
{
	// return the post as usual if no name given
	if (empty($name))
	{
		return $_POST;
	}

	// dropdowns are special
	if (isset($_POST['dropdown']))
	{
		foreach ($_POST['dropdown'] as $dropdown_name => $dropdown_values)
		{
			$_POST[$dropdown_name] = ($dropdown_values['hidden'] ?: $dropdown_values['visible']);
		}
	}

	// check the following arrays for the $name
	$check = [];
	if (isset($_POST)) { $check[] = $_POST; }
	if (isset($_GET)) { $check[] = $_GET; }
	if (isset($_SESSION)) { $check[] = $_SESSION; }
	if (isset($_COOKIE)) { $check[] = $_COOKIE; }
	foreach ($check as $check_inputs)
	{
		if (!empty($check_inputs))
		{
			foreach ($check_inputs as $key => $value)
			{
				if ($key == $name)
				{
					// preg replace if formatting is given
					if ($format)
					{
						$value = preg_replace('/[^'.$format.']/', '', $value);
					}

					// return the post var
					return $value;
				}
			}
		}
	}

	// check files
	if (!empty($_FILES[$name]))
	{
		$value = $_FILES[$name];
		$value['ext'] = explode('.', $_FILES[$name]['name']);
		$value['ext'] = array_pop($value['ext']);
		$value['ext'] = str_replace('jpeg', 'jpg', $value['ext']);

		return $value;
	}

	return '';
}
