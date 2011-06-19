<?php

class KoboldUtils{
	
	const CRYPT_SHA1 = 'sha1';
	const CRYPT_MD5 = 'md5';
	
	public static function redirect($url = null){
		if(is_null($url)){
			$url = $_SERVER['PHP_SELF'];
		}
        header('Location: '.$url);
        exit();
    }
    
	public static function httpAuth($user, $pass, $algorithm = CRYPT_SHA1, $realm = 'Secured Area'){
		if($algorithm == CRYPT_SHA1){
			$securePass = sha1($_SERVER['PHP_AUTH_PW']);
		}elseif($algorithm == CRYPT_MD5){
			$securePass = md5($_SERVER['PHP_AUTH_PW']);
		}
        if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] == $user && $securePass == $pass)){
            header('WWW-Authenticate: Basic realm="'.$realm.'"');
            header('Status: 401 Unauthorized');
            echo '<div align="center"><h1>Access denied!</h1></div>';
            exit();
        }
    }
    
	public static function validEmail($email, $testMx = false){
        if(preg_match("/^([_a-z0-9+-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email)){
            return true;
        }else{
            return false;
        }
    }
}