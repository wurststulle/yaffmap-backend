<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_addrMap' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class AddrMap extends BaseAddrMap {
	
	public function preInsert(PropelPDO $con = null){
		if($this->getId() == null){
		 	$this->setId(md5(mt_rand(1, 1000).date('U')));
		}
    	return true;
	}
	
	/**
	 * 
	 * @param string $mac
	 * @param string $ipv4
	 * @param string $ipv6
	 * @param boolean $create
	 * @return AddrMap
	 */
	public static function findOneAddrMapByAddr($mac, $ipv4, $ipv6, $bridgeName = null, $create = false){
		$addrMap = AddrMapQuery::create()
			->_if($mac != null)
				->filterByMacAddr($mac)
			->_endif()
			->_if($bridgeName != null)
				->filterByBridgeName($bridgeName)
			->_endif()
			->_if($ipv4 != null && $ipv6 != null)
				->filterByIpv4addr($ipv4)
				->filterByIpv6addr($ipv6)
			->_elseif($ipv4 != null)
				->filterByIpv4addr($ipv4)
			->_elseif($ipv6 != null)
				->filterByIpv6addr($ipv6)
			->_endif();
		if(!$create){
			return $addrMap->findOne();
		}else{
			return $addrMap->findOneOrCreate();
		}
	} 
	
	public static function isValidIpv4Addr($ip){
		if(preg_match("/^(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])$/", $ip)){
			return true;
		}
		return false;
	}
	
	public static function isValidIpv6Addr($ip){
		if(reg_match('/^\s*((([0-9A-Fa-f]{1,4}:){7}
(([0-9A-Fa-f]{1,4})|:))|(([0-9A-Fa-f]{1,4}:){6}
(:|((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|
[01]?\d{1,2})){3})|(:[0-9A-Fa-f]{1,4})))|
(([0-9A-Fa-f]{1,4}:){5}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){4}
(:[0-9A-Fa-f]{1,4}){0,1}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:){3}
(:[0-9A-Fa-f]{1,4}){0,2}((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})
(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|((:[0-9A-Fa-f]{1,4}){1,2})))|
(([0-9A-Fa-f]{1,4}:){2}(:[0-9A-Fa-f]{1,4}){0,3}((:((25[0-5]|2[0-4]\d|
[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
((:[0-9A-Fa-f]{1,4}){1,2})))|(([0-9A-Fa-f]{1,4}:)(:[0-9A-Fa-f]{1,4}){0,4}
((:((25[0-5]|2[0-4]\d|[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
((:[0-9A-Fa-f]{1,4}){1,2})))|(:(:[0-9A-Fa-f]{1,4}){0,5}((:((25[0-5]|2[0-4]\d|
[01]?\d{1,2})(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})?)|
((:[0-9A-Fa-f]{1,4}){1,2})))|(((25[0-5]|2[0-4]\d|[01]?\d{1,2})
(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?\s*$/')){
			return true;
		}
		return false;
	}
	
	/**
	 * @return sAddrMap
	 */
	public function getSoapClass(){
		$n = new sAddrMap();
		$n->id = $this->getId();
		$n->ipv4Addr = $this->getIpv4Addr();
		$n->ipv6Addr = $this->getIpv6Addr();
		$n->macAddr = $this->getMacAddr();
		$n->bridgeName = $this->getBridgeName();
		if($this->getIsGlobalUpdated()){
			$n->isGlobalUpdated = 'true';
		}else{
			$n->isGlobalUpdated = 'false';
		}
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
} // AddrMap
