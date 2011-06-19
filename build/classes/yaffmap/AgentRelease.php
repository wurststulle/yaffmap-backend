<?php



/**
 * Skeleton subclass for representing a row from the 'yaffmap_agentRelease' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.yaffmap
 */
class AgentRelease extends BaseAgentRelease {
	
	/**
	 * @return sAgentRelease
	 */
	public function getSoapClass(){
		$ar = new sAgentRelease();
		$ar->release = $this->getRelease();
		$ar->subRelease = $this->getSubRelease();
		$ar->releaseDate = $this->getReleaseDate();
		$ar->tree = $this->getUpgradeTree();
		$ar->version = $this->getVersion();
		$ar->agent = base64_encode(stream_get_contents($this->getAgent()));
		$ar->agentSize = $this->getAgentSize();
		return $ar;
	}
	
	/**
	 * return latest agent release of given tree and version
	 * 
	 * @param string $tree
	 * @param string $version
	 * @return AgentRelease
	 */
	public function getLatest($version, $tree){
		$con = Propel::getConnection(AgentReleasePeer::DATABASE_NAME);
		$sql = 'select * from '.AgentReleasePeer::TABLE_NAME.' t1 where t1.version = :version and t1.tree = :tree and t1.release = 
		(select max(t2.release) from yaffmap_agentRelease t2 where  t2.version = t1.version and t2.tree = t1.tree) 
		and t1.subrelease = (select max(t3.subRelease) from yaffmap_agentRelease t3 where  t3.version = t1.version and t3.tree = t1.tree)';
		$stmt = $con->prepare($sql);
		$stmt->execute(array(':version' => $version, ':tree' => $tree));
		$formatter = new PropelObjectFormatter();
		$formatter->setClass('AgentRelease');
		return $formatter->formatOne($stmt);
	}
	
	/**
	 * return agent download url
	 * 
	 * @return string url
	 */
	public function getUrl(){
		return YaffmapConfig::get('url').'/download/index.php?getFile='.$this->getId();
	}
} // AgentRelease
