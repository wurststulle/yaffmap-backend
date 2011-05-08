<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_ipAlias' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class IpAlias extends BaseIpAlias {
	
	public function preInsert(PropelPDO $con = null){
		if($this->getId() == null){
		 	$this->setId(md5(mt_rand(1, 1000).date('U')));
		}
    	return true;
	}	
	
	/**
	 * 
	 * Enter description here ...
	 * @param AddrMap $addrMap
	 * @param unknown_type $name
	 * @param unknown_type $ipv4
	 * @param unknown_type $ipv6
	 * @param unknown_type $create
	 */
	public static function findOneIpAlias($addrMap, $name, $ipv4, $ipv6, $create = false){
		$ipAlias =  IpAliasQuery::create()
			->_if($name != null)
				->filterByName($name)
			->_endif()
			->_if($addrMap != null)
				->filterByAddrMap($addrMap)
			->_endif()
			->_if($ipv4 != null && $ipv6 != null)
				->filterByIpv4Addr($ipv4)
				->filterByIpv6Addr($ipv6)
			->_elseif($ipv4 != null)
				->filterByIpv4Addr($ipv4)
			->_elseif($ipv6 != null)
				->filterByIpv6Addr($ipv6)
			->_endif();
		if(!$create){
			return $ipAlias->findOne();
		}else{
			return $ipAlias->findOneOrCreate();
		}
	}
	
	/**
	 * @return sIpAlias
	 */
	public function getSoapClass(){
		$n = new sIpAlias();
		$n->id = $this->getId();
		$n->ipv4Addr = $this->getIpv4Addr();
		$n->ipv6Addr = $this->getIpv6Addr();
		$n->name = $this->getName();
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
	
	/**
	 * @return IpAlias
	 */
	public static function createOne($device, $addrMap){
		$ipAlias = new IpAlias();
		$ipAlias->setId($device->id);
		$ipAlias->setIpv4Addr($device->ipv4Addr);
		$ipAlias->setIpv6Addr($device->ipv6Addr);
		$ipAlias->setName($device->name);
		$ipAlias->setCreatedAt($device->createdAt);
		$ipAlias->setUpdatedAt($device->updatedAt);
		$ipAlias->setAddrMap($addrMap);
		return $addrMap;
	}
} // IpAlias
