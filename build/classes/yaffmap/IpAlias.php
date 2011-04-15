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
} // IpAlias
