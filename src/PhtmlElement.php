<?php


/**
 * Class PhtmlElement
 */
class PhtmlElement
{

	/**
	 * @var null
	 */
	private $tag = null;

	/**
	 * @var null
	 */
	private $children = null;

	/**
	 * html attributes
	 */
	private $attributes = array();

	/**
	 * make sure we only create the content once
	 */
	private $isBuilt = false;

	/**
	 * @param null $tag
	 * @param null $attributes
	 * @param null $children
	 */
	public function __construct($tag = null, $attributes = null, $children = null)
	{
		$this->setTag($tag);
		$this->setAttributes($attributes);
		$this->add($children);
		$this->init();
	}

	/**
	 * Basic init stuff called after creation
	 */
	public function init() { }

	/**
	 * set html tag
	 *
	 * @param string $tag
	 */
	public function setTag($tag)
	{
		$this->tag = $tag;
	}

	/**
	 * set tag attributes
	 *
	 * @param array $attributes
	 */
	public function setAttributes($attributes = array())
	{
		settype($attributes, 'array');
		foreach($attributes as $attribute => $value) {
			if(strlen($attribute) > 0) {
				$this->setAttribute($attribute, $value);
			}
		}

	}

	/**
	 *
	 * @param string $attributName
	 *
	 * @return Ambigous <string, string>
	 */
	public function getAttributes($attributName = null)
	{
		if($attributName && isset($this->attributes[ $attributName ])) {
			return $this->attributes[ $attributName ];
		}
		elseif($attributName === null) {
			return $this->attributes;
		}
	}

	/**
	 * set single tag attribute
	 *
	 * @param string $attribute
	 * @param string $value
	 */
	public function setAttribute($attribute, $value)
	{
		if($attribute == 'class') {
			$this->addClass($value);
		}
		$this->attributes[ $attribute ] = $value;
	}

	/**
	 * add css class
	 *
	 * @param string $new_class_value
	 */
	public function add_class($new_class_value)
	{
		$res = array();
		if(array_key_exists('class', $this->attributes)) {
			$classes = explode(' ', $this->attributes['class']);
			foreach($classes as $class) {
				if(strlen($class) > 0) {
					$res[] = $class;
				}
			}
		}
		$res[] = $new_class_value;
		$this->attributes['class'] = implode(' ', $res);
	}

	public function __get($name)
	{
		if(array_key_exists($name, $this->attributes)) {
			return $this->attributes[ $name ];
		}
	}

	public function __set($name, $arg)
	{
		$this->setAttribute($name, $arg);
	}

	/**
	 *
	 * @param string $id
	 */
	public function set_id($id)
	{
		$this->setAttribute('id', $id);
	}

	/**
	 * add child elements
	 *
	 * @param PhtmlElement | string | null $element
	 */
	public function add($element = null)
	{
		if(is_null($element) === false) {
			$this->children[] = $element;
		}
	}

	/**
	 * remove all children
	 */
	public function removeChildren()
	{
		$this->children = array();
	}

	/**
	 * Has this element children?
	 *
	 * @return boolean
	 */
	public function hasChildren()
	{
		return count($this->children) > 0;
	}

	/**
	 * wrap content in html element
	 *
	 * @param string $tag
	 * @param string $attributes
	 * @param string $content
	 *
	 * @return string
	 */
	public function wrapInTag($tag = null, $attributes = null, $content = null)
	{
		$is_tag = (!is_null($tag));
		$short_tag = (!is_null($tag) && in_array($tag, explode(',', 'img,br,hr,link,input,meta')));
		// $close_short_tags = explode(',','img,br,hr,link,input,meta');
		// $close_short_tag = ($short_tag && in_array(jb_cf::get(cf_doctype), array(
		//		cf_doctype_html4_strict, cf_doctype_xhtml_mobile
		//	)));
		$close_short_tag = false;

		if($is_tag) {
			$param_string = PhtmlString::createAttributeString($attributes);
			$c[] = '<' . $tag . ((strlen($param_string)) ? ' ' . $param_string : '') . ($close_short_tag ? '/' : '') . '>';
		}
		$c[] = PhtmlString::stringify($content);
		if(!$short_tag && $is_tag) {
			$c[] = '</' . $tag . '>';
		}

		return implode('', $c);
	}

	/**
	 * create content - overwrite this method
	 */
	public function build() { }

	/**
	 * make sure we build it only once.
	 */
	public function buildTrigger()
	{
		if($this->isBuilt === false) {
			$this->build();
			$this->isBuilt = true;
		}
	}

	public function get()
	{
		$this->buildTrigger();

		return self::wrapInTag($this->tag, $this->attributes, $this->children);
	}

	public function __toString()
	{
		return (string)$this->get();
	}
}
