<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_config' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap
 */
class Config extends BaseConfig {
	
	/**
	 * unique identifier of the backend, generated out of the backends url
	 * @var string
	 */
	protected $id;
	
	/**
	 * get the backends id
	 * @return string
	 */
	public function getId(){
		return md5($this->url);
	}
	
	/**
	 * get configuration from database
	 * @return Config
	 * @throws YaffmapException
	 */
	public static function getConfig(){
		$conf = ConfigQuery::create()->findOne();
		if($conf == null){
			throw new YaffmapException('config not found.');
		}else{
			return $conf;
		}
	}
} // Config
