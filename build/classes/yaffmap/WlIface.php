<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_wlIface' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class WlIface extends BaseWlIface {

	public function preInsert(PropelPDO $con = null){
		if($this->getId() == null){
		 	$this->setId(md5(mt_rand(1, 1000).date('U')));
		}
    	return true;
	}
	
	public static function createDummy(WlDevice $wlDevice, AddrMap $addrMap){
		$dummyWlInterface = new WlIface();
		$dummyWlInterface->setAddrMap($addrMap);
		$dummyWlInterface->setWlDevice($wlDevice);
		$dummyWlInterface->setIsDummy(1);
		$dummyWlInterface->save();
		return $dummyWlInterface;
	}
	
	/**
	 * 
	 * @param unknown_type $device
	 * @param unknown_type $node
	 * @return WlIface
	 */
	public static function createOne($device, $wlDevice){
		$wlIface = new WlIface();
		$wlIface->setId($device->id);
		$wlIface->setName($device->name);
		$wlIface->getWlMacAddr($device->wlMacAddr);
		$wlIface->getWlMode($device->wlMode);
		$wlIface->getBssid($device->bssid);
		$wlIface->getEssid($device->essid);
		$wlIface->getBridgeName($device->bridgeName);
		$wlIface->getCreatedAt($device->createdAt);
		$wlIface->getUpdatedAt($device->updatedAt);
		$wlIface->setWlDevice($wlDevice);
		return $wlIface;
	}
	
	/**
	 * @return sWlIface
	 */
	public function getSoapClass(){
		$n = new sWlIface();
		$n->id = $this->getId();
		$n->wlMacAddr = $this->getWlMacAddr();
		$n->name = $this->getName();
		$n->wlMode = $this->getWlMode();
		$n->bssid = $this->getBssid();
		$n->essid = $this->getEssid();
		$n->bridgeName = $this->getBridgeName();
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
} // WlIface
