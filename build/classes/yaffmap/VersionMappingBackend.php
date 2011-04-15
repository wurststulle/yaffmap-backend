<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_versionMappingBackend' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap
 */
class VersionMappingBackend extends BaseVersionMappingBackend {
	
	/**
	 * @return sVersionMappingAgent
	 */
	public function getSoapClass(){
		$ar = new sVersionMappingBackend();
		$ar->serverRelease = $this->getServerRelease();
		$ar->clientRelease = $this->getClientRelease();
		return $ar;
	}
} // VersionMappingBackend
