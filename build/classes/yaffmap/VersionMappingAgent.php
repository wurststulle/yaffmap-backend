<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_versionMappingAgent' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap
 */
class VersionMappingAgent extends BaseVersionMappingAgent {

	/**
	 * @return sVersionMappingAgent
	 */
	public function getSoapClass(){
		$ar = new sVersionMappingAgent();
		$ar->agentRelease = $this->getAgentRelease();
		$ar->agentSubRelease = $this->getAgentSubRelease();
		$ar->agentTree = $this->getAgentUpgradeTree();
		$ar->agentVersion = $this->getAgentVersion();
		$ar->backendRelease = $this->getBackendRelease();
		return $ar;
	}
} // VersionMappingAgent
