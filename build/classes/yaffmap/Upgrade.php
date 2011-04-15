<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_upgrade' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap
 */
class Upgrade extends BaseUpgrade {

	/**
	 * @return sAgentRelease
	 */
	public function getSoapClass(){
		$ar = new sAgentRelease();
		$ar->release = $this->getRelease();
		$ar->subRelease = $this->getSubRelease();
		$ar->releaseDate = $this->getReleaseDate();
		$ar->tree = $this->getUpgradeTree();
		$ar->url = $this->getUrl();
		$ar->version = $this->getVersion();
		return $ar;
	}
} // Upgrade
