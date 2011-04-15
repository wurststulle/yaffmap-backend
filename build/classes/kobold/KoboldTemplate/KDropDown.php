<?php

/**
 * abstract class for select options (<option>)
 */
abstract class KoboldTemplateDropdownOption extends KoboldTemplate{}

/**
 * abstract class for select (<select>)
 */
abstract class KoboldTemplateDropdown extends KoboldTemplate implements IKoboldTemplateDisabled{
	
	/**
	 * disables this element
	 */
	public function disabled(){
		$this->addAttribute(array('disabled' => 'disabled'));
	}
}

class KSelect extends KoboldTemplateDropdown{

	public function addItem($item){
		if($item instanceof KoboldTemplateDropdownOption){
			if(is_array($item)){
				// items given as array
				foreach($item as $val){
					$this->item[] = $val;
				}
			}else{
				// only one item is given
				$this->item[] = $item;
			}
		}else{
			die(get_class($this).' got incompatible item');
		}
	}
	
	public function get(){
		return $this->_get('select');
	}
	
	/**
	 * create option with given value and text, if selected is TRUE, item will be selected.
	 * 
	 * @param int $value
	 * @param string $text
	 * @param toolean $isSelected
	 * @return KOption
	 */
	public function addOption($value, $text, $isSelected = false){
		$option = new KOption();
		$option->addAttribute(array('value' => $value));
		if($isSelected){
			$option->addAttribute(array('selected' => 'selected'));
		}
		$option->addItem($text);
		$this->addItem($option);
		return $option;
	}
}

class KOption extends KoboldTemplateDropdownOption implements IKoboldTemplateDisabled{
	
	public function disabled(){
		$this->addAttribute(array('disabled' => 'disabled'));
	}
	
	public function selected(){
		$this->addAttribute(array('selected' => 'selected'));
	}
	
	public function get(){
		return $this->_get('option');
	}
}
?>