<?php

class KForm extends KoboldTemplate{
	
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
	
	public function get(){
		$out = '<form';
		foreach($this->attribute as $key => $value){
			$out .= ' '.$key.'="'.$value.'"';
		}
		$out .= '>';
		foreach($this->item as $value){
			if($value instanceof KoboldTemplate){
				$out .= $value->get();
			}else{
				$out .= $value;
			}
		}
		$out .= '</form>';
		return $out;
	}
}
?>