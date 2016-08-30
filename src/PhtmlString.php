<?php

/**
 * Class PhtmlString
 */
class PhtmlString
{

	/**
	 * remove newlines in a string. experimental, since 24.1.2012
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function removeNewlines($string)
	{
		return preg_replace('/[\n\r]+/', '', $string);
	}

	/**
	 * replace <br>'s with correct <br>'s (with or without ending-slash)
	 *
	 * @param unknown $str
	 *
	 * @return mixed
	 */
	public static function nl2br($str)
	{
		return preg_replace('|<br />|', Phtml::br(), nl2br($str));
	}

	/**
	 * @param array $attributes
	 *
	 * @return string
	 */
	public static function createAttributeString($attributes = array())
	{
		settype($attributes, 'array');
		$stringParts = array();
		foreach($attributes as $key => $value) {
			$stringParts[] = $key . '="' . $value . '"';
		}

		return implode(' ', $stringParts);
	}

	/**
	 * Try to make a string from anything
	 *
	 * @param string | int | object | array $child
	 *
	 * @return string
	 */
	public static function stringify($child)
	{
		if(is_null($child)) {
			return '';
		}
		$c = array();
		switch(gettype($child)) {
			case 'boolean':
				$c[] = ($child ? 1 : 0);
				break;
			case 'integer':
			case 'string':
				$c[] = $child;
				break;
			case 'array':
				foreach($child as $tmp) {
					if(!is_null($tmp)) {
						$c[] = self::stringify($tmp);
					}
				}
				break;
			case 'object':
				if(method_exists($child, 'get')) {
					$c[] = $child->get();
				}
				else {
					$c[] = '*' . get_class($child) . ' ? *';
				}
				break;
		}

		return implode(PHP_EOL, $c);
	}
}