<?php

class Security
{

	public static function array_strip_tags($array)
	{
	    $result = array();
	    foreach ($array as $key => $value) {
	        $key = strip_tags($key);
	        if (is_array($value)) {
	            $result[$key] = self::array_strip_tags($value);
	        }
	        else {
	            $result[$key] = trim(html_entity_decode(strip_tags($value), ENT_QUOTES, 'utf-8'));
	        }
	    }
	    return $result;
	}

}
