<?php
class KA extends KoboldTemplate{
	
	public function get(){
		return $this->_get('a');
	}
}

class KSimpleLink extends KA{
	
	/**
	 * @param unknown_type $href
	 * @param unknown_type $text
	 */
	public function __construct($href, $text){
		$this->addAttribute(array('href' => $href));
		$this->addItem($text);
	}
}
?>