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
			->_if(!is_null($ipv4) && !is_null($ipv6))
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
	
	/**
	 * returns one address (mac or ipv4/6) of this addressMap that is not null
	 */
	public function getAddr(){
		if($this->getIpv4addr() != null && $this->getIpv4addr() != ''){
			return $this->getIpv4addr();
		}elseif($this->getIpv6addr() != null && $this->getIpv6addr() != ''){
			return $this->getIpv6addr();
		}elseif($this->getMacAddr() != null && $this->getMacAddr() != ''){
			return $this->getMacAddr();
		}else{
			return null;
		}
	}
	
	public static function isValidIpv4Addr($ip){
		if(preg_match("/^(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])\\.(\\d|[1-9]\\d|1\\d\\d|2[0-4]\\d|25[0-5])$/", $ip)){
			return true;
		}
		return false;
	}
	
	public static function isValidIpv6Addr($ip){
		if(preg_match('/^\s*((([0-9A-Fa-f]{1,4}:){7}
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
(\.(25[0-5]|2[0-4]\d|[01]?\d{1,2})){3})))(%.+)?\s*$/', $ip)){
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
	
	/**
	 * @return AddrMap
	 */
	public static function createOne($device){
		$addrMap = new AddrMap();
		$addrMap->setId($device->id);
		$addrMap->setIpv4Addr((($device->ipv4Addr == '')?NULL:$device->ipv4Addr));
		$addrMap->setIpv6Addr((($device->ipv6Addr == '')?NULL:$device->ipv6Addr));
		$addrMap->setMacAddr((($device->macAddr == '')?NULL:$device->macAddr));
		$addrMap->setBridgeName((($device->bridgeName == '')?NULL:$device->bridgeName));
		$addrMap->setIsGlobalUpdated($device->isGlobalUpdated);
		$addrMap->setCreatedAt($device->createdAt);
		$addrMap->setUpdatedAt($device->updatedAt);
		return $addrMap;
	}
	
	/**
	* delete AddrMaps(and constraints) that dont have been updated since $nbHours hours
	* @param integer $nbHours
	* @return number of deleted rows
	*/
	public static function deleteOld($nbHours = 12){
		return AddrMapQuery::create()->filterByUpdatedAt(time() - $nbHours * 60 * 60, ModelCriteria::LESS_THAN)->delete();
	}
} // AddrMap
