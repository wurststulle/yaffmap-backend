<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_v_rpLinkLocation' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap/views
 */
class RpLinkLocation extends BaseRpLinkLocation {

	public function getSoapClass(){
		$link = new sRpLink();
		$link->cost = $this->getCost();
		$link->rx = $this->getRx();
		$link->tx = $this->getTx();
		$link->rp = $this->getRp();
		$link->metric = $this->getMetric();
		$link->sourceNodeID = $this->getSourceNodeID();
		$link->sourceLat = $this->getSourceLat();
		$link->sourceLon = $this->getSourceLon();
		$link->destNodeID = $this->getDestNodeID();
		$link->destLat = $this->getDestLat();
		$link->destLon = $this->getDestLon();
		return $link;
	}
} // RpLinkLocation
