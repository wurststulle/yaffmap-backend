<?php
/**
 * abstract class provides action listener
 */
abstract class KoboldListener{
	
	/**
	 * type of listener
	 * @var string
	 */
	protected $type;
	
	/**
	 * the action called by listener
	 * @var KAction
	 */
	protected $action;
	
	public function __construct($type, KAction $action){
		$this->action = $action;
		$this->type = $type;
	}
	
	/**
	 * returns this listener as array
	 * @return array 
	 */
	public function getListener(){
		return array($this->type => $this->action);
	}
}

/**
 * Create generic listener
 */
class KListenerGeneric extends KoboldListener{
	
	/**
	 * Create listener for given event with given action
	 * @param string $event
	 * @param KAction $action
	 */
	public function __construct($event, KAction $action){
		call_user_func_array('parent::__construct', array($event, $action));
	}
}

/**
 * Create listener for onclick event
 */
class KListenerOnclick extends KoboldListener{
	
	/**
	 * Create listener for onclick event
	 * @param KAction $action
	 */
	public function __construct(KAction $action){
		call_user_func_array('parent::__construct', array('onclick', $action));
	}
}

/**
 * Create listener for onblur event
 */
class KListenerOnblur extends KoboldListener{
	
	/**
	 * Create listener for onblur event
	 * @param KAction $action
	 */
	public function __construct(KAction $action){
		call_user_func_array('parent::__construct', array('onblur', $action));
	}
}

/**
 * Create listener for onsubmit event
 */
class KListenerOnsubmit extends KoboldListener{
	
	/**
	 * Create listener for onclick event
	 * @param KAction $action
	 */
	public function __construct(KAction $action){
		call_user_func_array('parent::__construct', array('onsubmit', $action));
	}
}

/**
 * Create listener for onchange event
 */
class KListenerOnchange extends KoboldListener{
	
	/**
	 * Create listener for onchange event
	 * @param KAction $action
	 */
	public function __construct(KAction $action){
		call_user_func_array('parent::__construct', array('onchange', $action));
	}
}
?>