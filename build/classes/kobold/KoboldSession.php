<?php

class KoboldSession{
	
	private final function __construct(){
    	die('do not do this!');
    }	
	
	public static function set($name, array $items){
		$_SESSION[$name] = $items;
	}
	
	public static function get($name){
		return $_SESSION[$name];
	}
	
	public static function delete($name){
		unset($_SESSION[$name]);
	}
	
	public static function exists($name, array $items = null){
		if($items == null){
	    	if(isset($_SESSION[$name])){
	    		return true;
	    	}
    	}else{
    		foreach($items as $item){
	    		if(!isset($_SESSION[$name][$item])){
		    		return false;
		    	}
    		}
    		return true;
    	}
    	return false;
	}
}
?>