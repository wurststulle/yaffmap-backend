<?php
require_once 'KoboldCookie.php';
require_once 'KoboldSession.php';

class KoboldAuth{
	
	private static $auth = null;
	
	private static $salt = 'qwertz';
	
	private $userName = null;
	private $userId = null;
	
	private $loggedIn = false;

	public function __construct(){
		
	}
	
	public static function getAuth(){
		if(is_null(self::$auth)){
			self::$auth = new KoboldAuth();
		}
		return self::$auth;
	}
	
	public function login($userName, $passwd, $setCookie = false){
		
	}
	
}

class KAuth extends KoboldAuth{
	
}

?>