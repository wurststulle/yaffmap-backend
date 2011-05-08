<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_wiredIface' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class WiredIface extends BaseWiredIface {

	/**
	 * @return sWiredIface
	 */
	public function getSoapClass(){
		$n = new sWiredIface();
		$n->name = $this->getName();
		$n->bridgeName = $this->getBridgeName();
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
	
	/**
	 * @return WiredIface
	 */
	public static function createOne($device, $node, $addrMap){
		$wiredIface = new WiredIface();
		$wiredIface->setName($device->name);
		$wiredIface->setBridgeName($device->bridgeName);
		$wiredIface->setCreatedAt($device->createdAt);
		$wiredIface->setUpdatedAt($device->updatedAt);
		$wiredIface->setFfNode($node);
		$wiredIface->setAddrMap($addrMap);
		return $wiredIface;
	}
} // WiredIface
