<?php
/**
 * creates action to be executed in KoboldListener
 */
class KAction{
	
	protected $action;
	
	/**
	 * create new KAction
	 * @param string $action action to be executed
	 */
	public function __construct($action){
		$this->action = $action;
	}
	
	public function __toString(){
		return $this->action;
	}
}
?>