<?php
class KDiv extends KoboldTemplate{
	
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
		return $this->_get('div');
	}
}

class KSpan extends KoboldTemplate{
	
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
		return $this->_get('span');
	}
}

class KLayer extends KoboldTemplate{
	
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
		$out = '';
		foreach($this->item as $value){
			$out .= $value;
		}
		return $out;
	}
}
?>