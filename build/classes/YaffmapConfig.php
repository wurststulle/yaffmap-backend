<?php

class YaffmapConfig{
	
	/**
	 * @var Config
	 */
	private static $config = null;
	
	private function init(){
		self::$config = ConfigQuery::create()->findOne();
		if(is_null(self::$config)){
			throw new YaffmapException('config not found.');
		}
		$confFromFile = parse_ini_file('config.inc', true);
		foreach($confFromFile as $key => $value){
			self::$config->$key = $value;
		}
	}
	
	/**
	 * get configuration
	 * @return Config
	 * @throws YaffmapException
	 */
	public static function getConfig(){
		if(is_null(self::$config)){
			self::init();
		}
		return self::$config;
	}
	
	/**
	 * get requested configuration element
	 * @return string
	 * @throws YaffmapException
	 */
	public static function get($var){
		if(is_null(self::$config)){
			self::init();
		}
		if(method_exists(self::$config, 'get'.ucfirst($var))){
			return self::$config->{'get'.ucfirst($var)}();
		}elseif(property_exists(self::$config, $var)){
			return self::$config->$var;
		}else{
			throw new YaffmapException('Config element '.$var.' not found.');
		}
	}
}
?>