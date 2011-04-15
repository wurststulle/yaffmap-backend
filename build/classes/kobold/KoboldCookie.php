<?php

class KoboldCookie{
	
	private static $_seperator = '-:-';
	private static $_uniqueID = 'Ju?hG&F0yh9?=/6*GVfd-d8u6f86hp';

    private function __construct(){
    	die('do not do this!');
    }
    
    /**
     * build cookie string
     * 
     * @param unknown_type $var_array
     */
	private function _build_cookie($var_array){
		$out = '';
		if(is_array($var_array)){
			foreach($var_array as $index => $data){
				$out .= ($data != "") ? $index."=".$data."|" : "";
			}
		}
		return rtrim($out,"|");
	}
	
	/**
	 * split cookiestring
	 * 
	 * @param unknown_type $cookie_string
	 */
	private function _break_cookie($cookie_string){
		$array = explode("|", $cookie_string);
		foreach($array as $i => $stuff){
			$stuff = explode("=", $stuff);
			$array[$stuff[0]] = $stuff[1];
			unset($array[$i]);
		}
		return $array;
	}
    
    /**
     * Delete a cookie
     * 
     * @param unknown_type $key
     * @param unknown_type $path
     */
    public static function delete($name, $path = ''){
    	if(isset($_COOKIE[$name])){
    		setcookie($name, false, time() - 3600, $path);
    		unset($_COOKIE[$name]);
    	}
    }
    
    /**
     * get cookie with given name
     * 
     * @param $name
     */
    public static function get($name){
    	if(isset($_COOKIE[$name])){
    		// cookie exists
	    	$cut = explode(self::$_seperator, $_COOKIE[$name]);
			if(md5($cut[0].self::$_uniqueID) === $cut[1]){
				// cookie is not manipulated
	       		return self::_break_cookie($cut[0]);
			}else{
				die('Cookie data is invalid!!!');
			}
    	}
    	return null;
    }
    
    /**
     * determine whether cookie with given name (and optional given items) exists
     * 
     * @param $name
     * @param $items optional array of strings to check for
     */
    public static function exists($name, array $items = null){
    	if($items == null){
	    	if(isset($_COOKIE[$name])){
	    		return true;
	    	}
    	}else{
    		$cookie = self::get($name);
    		foreach($items as $item){
	    		if(!isset($cookie[$item])){
		    		return false;
		    	}
    		}
    		return true;
    	}
    	return false;
    }
    
    /**
     * return all cookies
     * 
     * @return array cookie array
     */
    public static function contents(){
        return $_COOKIE;
    }
    
    /**
     * Set cookie information
     * 
     * @param unknown_type $name
     * @param unknown_type $value array of values
     * @param unknown_type $expire expire time
     * @param unknown_type $path path
     * @param unknown_type $domain domain
     * @param unknown_type $secure Does this cookie need a secure HTTPS connection? 
     * @param unknown_type $httponly Can non-HTTP services access this cookie (IE: javascript)?
     */
    public static function set($name, array $value, $expire = 0, $path = '', $domain = '', $secure = false, $httponly = true){
    	if(self::exists($name)){
    		// if cookie exists already, copy old content
    		$cookie = KoboldCookie::get($name);
    		KoboldCookie::delete($name, $path);
    		$cookie_string = self::_build_cookie(array_merge($value, $cookie));
    	}else{
    		$cookie_string = self::_build_cookie($value);
    	}
		setcookie($name, $cookie_string.self::$_seperator.md5($cookie_string.self::$_uniqueID), $expire, $path, $domain, $secure, $httponly);  
    }
}
?>