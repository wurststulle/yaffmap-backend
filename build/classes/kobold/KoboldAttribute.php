<?php

/**
 * interface for attributes that could have more than one value, e.g. style
 */
interface IKoboldAttributeExtendable{}

class KoboldAttribute{
	
	public $name;
	public $value;
	
	/**
	 * @param string $name name of attribute
	 * @param string $value value of attribute
	 */
	public function __construct($name, $value){
		$args = func_get_args();
		$this->name = $name;
		$this->value = $value;
	}
}

class KAttribute extends KoboldAttribute{}

class KAttributeSrc extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('src', $value));
	}
}

class KAttributeValue extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('value', $value));
	}
}

class KAttributeId extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('id', $value));
	}
}

class KAttributeName extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('name', $value));
	}
}

class KAttributeWidth extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('width', $value));
	}
}

class KAttributeHeight extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('height', $value));
	}
}

class KAttributeColspan extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('colspan', $value));
	}
}

class KAttributeClass extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('class', $value));
	}
}

class KAttributeDisabled extends KoboldAttribute{
	
	public function __construct(){
		$this->name = 'disabled';
		call_user_func_array('parent::__construct', array('disabled'));
	}
}

class KAttributeStyle extends KoboldAttribute implements IKoboldAttributeExtendable{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('style', $value));
	}
}

class KAttributeStyleDisplayNone extends KAttributeStyle{
	
	public function __construct(){
		call_user_func_array('parent::__construct', array('display:none;'));
	}
}

class KAttributeStyleBackground extends KAttributeStyle{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('background:'.$value.';'));
	}
}

/**
 * create placeholder for input fields (eg. KTextfield)
 *
 */
class KAttributePlaceholder extends KoboldAttribute{
	
	public function __construct($value){
		call_user_func_array('parent::__construct', array('placeholder', $value));
	}
}
?>