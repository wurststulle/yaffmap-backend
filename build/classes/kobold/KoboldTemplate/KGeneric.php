<?php

/**
 * abstract class provides basic function for templates
 */
abstract class KoboldTemplate implements IKoboldTemplate{
	
	/**
	 * collection of tag-attributes
	 * @var unknown_type
	 */
	protected $attribute = array();
	
	/**
	 * collection of child-items
	 * @var unknown_type
	 */
	protected $item = array();
	
	/**
	 * 
	 * @example new KoboldTemplate(array('attr1' => 1), array('item1', 'item2'));
	 * @param array $attr
	 * @param array $items
	 */
	public function __construct(array $attr = null, array $items = null){
		if($attr != null){
			foreach($attr as $key => $value){
				$this->addAttribute(array($key => $value));
			}
		}
		if($items != null){
			foreach($items as $val){
				$this->addItem($val);
			}
		}
	}
	
	/**
	 * adds an attribute 
	 * 
	 * @example addAttribute(array('a' => 1, '2' => 2));
	 * @param array $attr
	 * @param bool $strict overwrites existing attribute if true
	 */
	public function addAttribute(array $attr, $strict = true){
		foreach($attr as $key => $value){
			if($value instanceof KoboldAttribute){
				// KAttribute or KoboldAttribute given
				if(array_key_exists('IKoboldAttributeExtendable', class_implements($value))){
					// attribute could have more than one value e.g. style
					// append value
					$this->attribute[$value->name] .= ' '.$value->value;
				}else{
					if(array_key_exists($value->name, $this->attribute)){					
						// attribute already exists
						if($strict){
							// overwrite key
							$this->attribute[$value->name] = $value->value;
						}
					}else{
						$this->attribute[$value->name] = $value->value;
					}
				}
			}elseif($value instanceof KoboldListener){
				// KoboldListener given
				$this->addListener($value);
			}else{
				// attribute given as array('attr' => 'val')
				if(array_key_exists($key, $this->attribute)){
					// if key already exists
					if($strict){
						// overwrite key
						$this->attribute[$key] = $value;
					}
				}else{
					// if key does not already exist
					$this->attribute[$key] = $value;
				}
			}
		}
	}
	
	/**
	 * adds listener to item
	 * @example 
  	 * $b = new KButton();
 	 * $b->addListener(new KListenerOnclick(new KAction('doIt();')));
	 * 
	 * @param KoboldListener $listener
	 */
	public function addListener($listener){
		$this->addAttribute($listener->getListener());
	}

	/**
	 * returns value of attribute with given name, NULL if attribute does not exist
	 * 
	 * @param $name name of attribute
	 */
	public function getAttribute($name){
		if(array_key_exists($name, $this->attribute)){
			return $this->attribute[$name];
		}else{
			return null;
		}
	}
	
	/**
	 * adds an item to the item collection
	 * @example addItem(array('a', '2'));
	 * @todo - check for instance of koboldtemplate in item if its an array
	 * 		 - force addItem(array $item) ?
	 * @param array/string $item item to be added
	 */
	public function addItem($item){
		if(is_array($item)){
			// array of items given
			foreach($item as $val){
				if($val instanceof KoboldTemplate){
					// its not allowed to add a template item itself
					die(get_class($this).' got incompatible item');
				}else{
					$this->item[] = $val;
				}
			}
		}else{
			// one item given
			if($item instanceof KoboldTemplate){
				// its not allowed to add a template item itself
				die(get_class($this).' got incompatible item');
			}else{
				$this->item[] = $item;
			}
		}
	}
	
	protected function _get($tag){
		$out = '<'.$tag;
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= '>';
		foreach($this->item as $value){
			$out .= $value;
		}
		$out .= '</'.$tag.'>';
		return $out;
	}
	
	/**
	 * adds &nbsp;
	 * 
	 * @return &nbsp;
	 */
	public function addBlanc(){
		return new KBlanc();
	}
	
	/**
	 * adds <br>
	 * 
	 * @return KBr
	 */
	public function addBr(){
		return new KBr();
	}
	
	/**
	 * sorts an array with multible items
	 * 
	 * @example $result = multisort($test, array('a','b'));
	 * @param unknown_type $array
	 * @param unknown_type $sort_by
	 * @param boolean reverse
	 */
	public static function multisort($array, $sortBy, $reverse = false){
	    foreach($array as $key => $value){
	        $evalstring = '';
	        foreach($sortBy as $sort_field){
	            $tmp[$sort_field][$key] = $value[$sort_field];
	            $evalstring .= '$tmp[\''.$sort_field.'\'], ';
	        }
	    }
	    $evalstring .= '$array';
	    $evalstring = 'array_multisort('.$evalstring.');';
	    eval($evalstring);
	    if($reverse){
	    	return array_reverse($array);
	    }else{
	    	return $array;
	    }
	}
	
	public function __call($name, $args){
		$methode = str_split($name, 12);
		switch($methode[0]){
			case 'setAttribute':
				$class = 'KAttribute'.$methode[1];
				$this->addAttribute(array(new $class($args[0])));
				break;
			default:
				throw new Exception('Methode "'.$name.'" not found!');
		}
	}
	
	public function __toString(){
		return $this->get();
	}
}

class KGenericTag extends KoboldTemplate{
	
	/**
	 * tag name
	 * @var string
	 */
	private $_name;
	
	/**
	 * @param string $name tag name
	 */
	public function __construct($name){
		$this->_name = $name;
	}
	
	public function get(){
		return $this->_get($this->_name);
	}
}

class KBr extends KoboldTemplate{
	
	public function get(){
		return '<br />';
	}
}

class KHr extends KoboldTemplate{
	
	public function get(){
		return '<hr />';
	}
}

class KBlanc extends KoboldTemplate{
	
	public function get(){
		return '&nbsp;';
	}
}

class KPre extends KoboldTemplate{
	
	/**
	 * @param array $items 
	 */
	public function __construct(array $items){
		foreach($items as $item){
			$this->addItem($item);
		}
	}
	
	public function get(){
		return $this->_get('pre');
	}
}

class KBlockquote extends KoboldTemplate{
	
	public function addItem($item){
		if(is_array($item)){
			foreach($item as $val){
				$this->item[] = $val;
			}
		}else{
			$this->item[] = $item;
		}
	}
	
	public function get(){
		return $this->_get('blockquote');
	}
}

class KScript extends KoboldTemplate{
	
	public function __construct($src){
		// call parent constructor
//		$args = func_get_args();
//		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'text/javascript'), false);
		$this->addAttribute(array('src' => $src));
	}
	
	public function get(){
		return $this->_get('script');
	}
}

/**
 * class for linking style sheets
 */
class KLink extends KoboldTemplate{
	
	/**
	 * links style sheet
	 * @param string $href
	 */
	public function __construct($href){
		$this->addAttribute(array('href' => $href));
		$this->addAttribute(array('rel' => 'stylesheet'));
		$this->addAttribute(array('type' => 'text/css'));
	}
	
	public function get(){
		$out = '<link';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= ' \>';
		return $out;
	}
}

class KImage extends KoboldTemplate{
	
	public function __construct($src){
		$this->addAttribute(array('src' => $src));
	}
	
	public function get(){
		$out = '<img';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= ' \>';
		foreach($this->item as $value){
			$out .= $value;
		}
		return $out;
	}
}
?>