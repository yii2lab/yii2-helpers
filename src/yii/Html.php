<?php

namespace yii2lab\helpers\yii;

use yii\helpers\Html as YiiHtml;

class Html extends YiiHtml
{
	
	public static function fa($icon, $options = [], $prefix = 'fa fa-', $tag = 'i')
	{
		return self::icon($icon, $options, 'fa fa-', $tag);
	}
	
	public static function icon($icon, $options = [], $prefix = 'fa fa-', $tag = 'i')
	{
		if(!is_array($options)) {
			$type = $options;
			$options = [];
			$options['class'] = $type ? ' text-' . $type : '';
		} else {
			$options['class'] = !empty($options['class']) ? $options['class'] : '';
		}
		
		$options['class'] = $prefix . $icon . ' ' . $options['class'];
		return static::tag($tag, '', $options);
	}

}
