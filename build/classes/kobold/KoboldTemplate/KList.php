<?php

abstract class KoboldTemplateList extends KoboldTemplate{
	
	public function addItem($item){
//		if(!($item instanceof KoboldTemplateListElement)){
//			die(get_class($this).' got incompatible item');
//		}else{
			if(is_array($item)){
				// items given as array
				foreach($item as $val){
					$this->item[] = $val;
				}
			}else{
				// only one item is given
				$this->item[] = $item;
			}
//		}
	}
	
	public function addListElement($item){
		if(is_array($item)){
			foreach($item as $val){
				$li = new KListItem();
				$li->addItem(array($val));
				$this->addItem($li);
			}
		}else{
			$li = new KListItem();
			$li->addItem(array($item));
			$this->addItem($li);
		}
	}
}

abstract class KoboldTemplateListElement extends KoboldTemplate{
	
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
}

class KListOrdered extends KoboldTemplateList{
	
	public function get(){
		return $this->_get('ol');
	}
}

class KListUnordered extends KoboldTemplateList{

	public function get(){
		return $this->_get('ul');
	}
}

class KListItem extends KoboldTemplateListElement{
	
	public function get(){
		return $this->_get('li');
	}
}
?>