<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_rfLinkOneWay' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class RfLinkOneWay extends BaseRfLinkOneWay {
	
	/**
	* delete RfLinkOneWay links that dont have been updated since $nbHours hours
	* @param integer $nbHours
	* @return number of deleted rows
	*/
	public static function deleteOld($nbHours = 12){
		return RfLinkOneWayQuery::create()->filterByUpdatedAt(time() - $nbHours * 60 * 60, ModelCriteria::LESS_THAN)->delete();
	}
	
	public function getSoapClass(){
		$n = new sRfLinkOneWay();
		$n->createdAt = $this->getCreatedAt();
		$n->destMac = $this->getDestMac();
		$n->rssi = $this->getRssi();
		$n->txRate = $this->getTxRate();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
} // RfLinkOneWay
