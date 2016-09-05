<?php

/**
 * Class Phtml
 */
class Phtml
{
	/**
	 *
	 * @param unknown $name
	 * @param unknown $args
	 *
	 * @return PhtmlElement
	 */
	public static function __callStatic($name, $args)
	{
		$tags = explode(',', 'a,abbr,address,area,article,aside,audio,b,base,bdo,blockquote,body,br,button,canvas,caption,cite,code,col,colgroup,command,datalist,dd,del,details,dfn,div,dl,dt,em,embed,eventsource,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,i,iframe,img,input,ins,kbd,keygen,label,legend,li,link,mark,map,menu,meta,meter,nav,noscript,object,ol,optgroup,option,output,p,param,pre,progress,q,ruby,rp,rt,samp,script,section,select,small,source,span,strong,style,sub,summary,sup,table,tbody,td,textarea,tfoot,th,thead,time,title,tr,ul,var,video,wbr');
		if(in_array($name, $tags)) {
			return new PhtmlElement($name, (count($args) > 0 ? self::getval(0, $args) : null), (count($args) > 1 ? self::getval(1, $args) : null));
		}
	}

	/**
	 * ***** BASIC HTML ***************************************************
	 */
	/**
	 *
	 * @return PhtmlElement
	 */
	public static function blank($content = null)
	{
		return new PhtmlElement(null, array(), $content);
	}

	/**
	 * Set HTML Tagname
	 *
	 * @return PhtmlElement
	 */
	public static function element($tag, $attr = array(), $content = null)
	{
		return new PhtmlElement($tag, $attr, $content);
	}

	/**
	 * <script>
	 * @return PhtmlElement
	 */
	public static function script($attr = array(), $content = null)
	{
		// if(!array_key_exists('language', $attr))
		// $attr['language'] = 'Javascript';
		settype($attr, 'array');
		if(!array_key_exists('type', $attr))
			$attr['type'] = 'text/javascript';

		return new PhtmlElement('script', $attr, $content);
	}

	/**
	 * HTML Comment
	 *
	 * @param null $content
	 *
	 * @return string
	 */
	public static function comment($content = null)
	{
		return '<!-- ' . PhtmlString::stringify($content) . ' -->';
	}

	/**
	 * @param       $size
	 * @param array $attr
	 * @param null  $content
	 *
	 * @return PhtmlElement
	 */
	public static function h($size, $attr = array(), $content = null)
	{
		return self::element('h' . $size, $attr, $content);
	}

	/**
	 * @param       $src
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function img($src, $attr = array())
	{
		settype($attr, 'array');

		return self::element('img', array_merge(array(
			'alt' => 'img', 'src' => $src
		), $attr));
	}


	/**
	 * @param       $name
	 * @param null  $value
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputText($name, $value = null, $attr = array())
	{
		settype($attr, 'array');

		return new PhtmlElement('input', array_merge(array(
			'name' => $name, 'value' => $value
		), $attr));
	}

	/**
	 * @param       $name
	 * @param null  $value
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputEmail($name, $value = null, $attr = array())
	{
		settype($attr, 'array');

		return self::element('input', array_merge($attr, array(
			'name' => $name, 'value' => $value, 'type' => 'email'
		), $attr));
	}

	/**
	 * @param       $name
	 * @param null  $value
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputHidden($name, $value = null, $attr = array())
	{
		settype($attr, 'array');

		return self::element('input', array_merge(array(
			'type' => 'hidden', 'name' => $name, 'value' => $value
		), $attr));
	}

	/**
	 * @param       $name
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputPassword($name, $attr = array())
	{
		settype($attr, 'array');

		return self::element('input', array_merge(array(
			'type' => 'password', 'name' => $name
		), $attr));
	}

	/**
	 * @param       $name
	 * @param null  $value
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputTextarea($name, $value = null, $attr = array())
	{
		settype($attr, 'array');

		return self::element('textarea', array_merge(array(
			'name' => $name
		), $attr), $value);


		return $el;
	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $attr
	 * @param bool  $checked
	 *
	 * @return PhtmlElement
	 */
	public static function inputCheckbox($name, $value, $attr = array(), $checked = false)
	{
		settype($attr, 'array');

		$el = self::element('input', array_merge((array)$attr, array(
			'type' => 'checkbox', 'name' => $name, 'value' => $value
		)));
		if($checked === true) {
			$el->setAttribute('checked', 'checked');
		}

		return $el;
	}

	/**
	 * @param       $name
	 * @param array $opts
	 * @param null  $selected
	 * @param array $attr
	 * @param bool  $multi
	 * @param array $options
	 *
	 * @return PhtmlElement
	 */
	public static function inputSelect($name, array $opts, $selected = null, $attr = array(), $multi = false, $options = array())
	{
		settype($options, 'array');

		$useValuesAsKey = (isset($options['useValuesAsKey']) && $options['useValuesAsKey'] === true);
		$item = new PhtmlElement('select', $attr);
		if($multi) {
			$item->setAttribute('multiple', 'multiple');
			$name = $name . '[]';
		}
		$item->setAttribute('name', $name);
		foreach($opts as $key => $value) {
			$attr = array(
				'value' => ($useValuesAsKey ? $value : $key)
			);
			$select = false;
			if(!is_null($selected)) {
				if($multi && is_array($selected) && in_array($key, $selected)) {
					$select = true;
				}
				elseif($selected == $key || $selected == $value) {
					$select = true;
				}
			}
			if($select) {
				$attr['selected'] = 'selected';
			}
			$item->add(new PhtmlElement('option', $attr, $value));
		}

		return $item;
	}

	/**
	 * @param       $name
	 * @param       $value
	 * @param array $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputRadio($name, $value = 1, $attr = array())
	{
		return self::element('input', array_merge(array(
			'type' => 'radio', 'name' => $name, 'value' => $value
		), $attr));
	}

	/**
	 * @param string $name
	 * @param array  $attr
	 *
	 * @return PhtmlElement
	 */
	public static function inputFile($name = 'file', $attr = array())
	{
		return self::element('input', array_merge(array(
			'type' => 'file', 'name' => $name
		), $attr));
	}

	/**
	 * @param string $name
	 * @param string $label
	 * @param array  $attr
	 *
	 * @return PhtmlElement
	 */
	public static function buttonSubmit($name = 'save', $label = '', $attr = array())
	{
		$inp = self::element('button', array_merge(array(
			'name' => $name, 'type' => 'submit'
		), $attr));

		if($label) {
			$inp->setAttribute('value', $label);
		}

		return $inp;
	}

	/**
	 * @param $index
	 * @param $data
	 *
	 * @return null
	 */
	private static function getval($index, $data)
	{
		$res = null;
		if(is_array($data) && array_key_exists($index, $data)) {
			$res = $data[ $index ];
		}

		return $res;
	}
}
