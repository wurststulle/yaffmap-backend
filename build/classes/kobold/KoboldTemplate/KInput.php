<?php

class KInputfield extends KoboldTemplate implements IKoboldTemplateDisabled{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
	}
	
	public function get(){
		$out = '<input';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= ' />';
		return $out;
	}
	
	public function disabled(){
		$this->addAttribute(array('disabled' => 'disabled'));
	}
}

class KTextfield extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'text'));
	}
}

class KHiddenfield extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'hidden'));
	}
}

class KPasswordfield extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'password'));
	}
}

class KRadioButton extends KInputfield implements IKoboldTemplateChecked{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'radio'));
	}
	
	public function checked(){
		$this->addAttribute(array('checked' => 'checked'));
	}
	
	public function addItem($item){
		if(is_array($item)){
			// array of items given
			foreach($item as $val){
				$this->item[] = $val;
			}
		}else{
			// one item given
			$this->item[] = $item;
		}
	}
	
	public function get(){
		$out = '<input';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= ' />';
		foreach($this->item as $value){
			$out .= $value;
		}
		return $out;
	}
}
/**
 * @example
$grp = new KRadioButtonSet();
$grp->addAttribute(array('name' => 'myName'));
$grp->addRadioButton(array('id' => 'id1', 'value' => 1), array('hallo1'));
$grp->addRadioButton(array('id' => 'id2', 'value' => 2), array('hallo2'));
 * 
 * TODO construct ein array geben, dem konstructor übergebene attribute an radiobuttons übergeben
 *
 */
class KRadioButtonSet extends KoboldTemplate implements IKoboldTemplateDisabled{
	//<input type="radio" name="grp" value="0" id="grp_0" /> nein
	protected $count;
	
	/**
	 * 
	 * @param string $name name of radio buttin set
	 */
	public function __construct($name = null){
		$this->count = 0;
		if($name != null){
			$this->addAttribute(array('name' => $name));
		}else{
			$this->addAttribute(array('name' => 'radioButtonGroup'));
		}
	}
	
	/**
	 * TODO check for KRadioButton
	 * @param unknown_type $item
	 */
	public function addItem($item){
		if(is_array($item)){
			// items given as array
			foreach($item as $val){
				$this->item[] = $val;
			}
		}else{
			// only one item is given
			$this->item[] = $item;
		}
	}
	
	/**
	 * @param array $attr
	 * @param string $description
	 * @return KRadioButton
	 */
	public function addRadioButton(){
		$num = func_num_args();
		$args = func_get_args();
		$rb = new KRadioButton();
		$rb->addAttribute(array('name' => $this->getAttribute('name')));
		$rb->addAttribute(array('id' => $this->getAttribute('name').'_'.$this->count));
		foreach($args[0] as $key => $value){
			$rb->addAttribute(array($key => $value));
		}
		foreach($args[1] as $value){
			$rb->addItem(array($value));
		}
		$this->addItem($rb);
		return $rb;
	}
	
	public function disabled(){
		$this->addAttribute(array('disabled' => 'disabled'));
	}
	
	/**
	 * returns radiobuttonset as array
	 * @return array of KRadioButton
	 */
	public function getArray(){
		$out = array();
		foreach($this->item as $value){
			$out[] = $value;
		}
		return $out;
	}
	
	public function get(){
		$out = '';
		foreach($this->item as $value){
			$out .= $value->get();
		}
		return $out;
	}
}
//$grp = new KRadioButtonSet();
//$grp->addAttribute(array('name' => 'myName'));
//$grp->addRadioButton(array(array('value' => 1, 'checked' => 'checked'), array('value' => 2)));
//echo '<pre>';
//print_r ($grp->get());
//echo '</re>';

class KCheckedRadioButton extends KRadioButton{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('checked' => 'checked'));
	}
}

class KCheckbox extends KInputfield implements IKoboldTemplateChecked{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'checkbox'));
	}
	
	public function checked(){
		$this->addAttribute(array('checked' => 'checked'));
	}
}

class KButton extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'submit'));
	}
}

class KImageButton extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'image'));
	}
}

class KResetButton extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'reset'));
	}
}

class KFileInput extends KInputfield{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		$this->addAttribute(array('type' => 'file'));
	}
}

class KTextarea extends KoboldTemplate implements IKoboldTemplateDisabled{
	
	public function __construct(){
		// call parent constructor
		$args = func_get_args();
		call_user_func_array('parent::__construct', $args);
		// set default size
		$this->addAttribute(array('cols' => 45));
		$this->addAttribute(array('rows' => 5));
	}
	
	public function setSize($cols, $rows){
		$this->addAttribute(array('cols' => $cols, 'rows' => $rows));
	}
	
	public function disabled(){
		$this->addAttribute(array('disabled' => 'disabled'));
	}
	
	public function get(){
		$out = '<textarea';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= '>';
		foreach($this->item as $value){
			if($value instanceof KoboldTemplate){
				die(get_class($this).' got incompatible item');
			}
			$out .= $value;
		}
		$out .= '</textarea>';
		return $out;
	}
}
?>