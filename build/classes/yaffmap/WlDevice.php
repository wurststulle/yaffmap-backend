<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_wlDevice' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class WlDevice extends BaseWlDevice {
	
	public function preInsert(PropelPDO $con = null){
		if($this->getId() == null){
		 	$this->setId(md5(mt_rand(1, 1000).date('U')));
		}
    	return true;
	}
	
	public static function createDummy(FfNode $node){
		$dummyWlDevice = new WlDevice();
		$dummyWlDevice->setId(md5(mt_rand(1, 100000).date('U')));
		$dummyWlDevice->setFfNode($node);
		$dummyWlDevice->setIsDummy(1);
		$dummyWlDevice->save();
		return $dummyWlDevice;
	}
	
	/**
	 * 
	 * @param unknown_type $device
	 * @param unknown_type $node
	 * @return WlDevice
	 */
	public static function createOne($device, $node){
		$wlDevice = new WlDevice();
		$wlDevice->setId($device->id);
		$wlDevice->setName((($device->name == '')?NULL:$device->name));
		$wlDevice->setFfNode($node);
		return $wlDevice;
	}
	
	/**
	 * @return sWlDevice
	 */
	public function getSoapClass(){
		$n = new sWlDevice();
		$n->id = $this->getId();
		$n->name = $this->getName();
		$n->txpower = $this->getTxpower();
		$n->antDirection = $this->getAntDirection();
		$n->antBeamH = $this->getAntBeamH();
		$n->antBeamV = $this->getAntBeamV();
		$n->antGain = $this->getAntGain();
		$n->antTilt = $this->getAntTilt();
		$n->antPol = $this->getAntPol();
		$n->channel = $this->getChannel();
		$n->wirelessStandard = $this->getWirelessStandard();
		$n->frequency = $this->getFrequency();
		$n->availFrequency = $this->getAvailFrequency();
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
} // WlDevice
