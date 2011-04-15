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
	
	public static function createDummy(FfNode $node){
		$dummyWlDevice = new WlDevice();
		$dummyWlDevice->setFfNode($node);
		$dummyWlDevice->setIsDummy(1);
		$dummyWlDevice->save();
		return $dummyWlDevice;
	}
} // WlDevice
