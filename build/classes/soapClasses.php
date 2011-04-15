<?php
require_once 'kobold/Kobold.php';

class SoapClassMap{
	
	public static function getMap(){
		return array(
			'FfNode' => 'sFfNode', 
			'ArrayOfFfNodes' => 'sArrayOfFfNodes', 
			'Backend' => 'sBackend', 
			'AgentRelease' => 'sAgentRelease', 
			'ArrayOfAgentRelease' => 'sArrayOfAgentRelease', 
			'ArrayOfVersionMappingAgent' => 'sArrayOfVersionMappingAgent', 
			'VersionMappingAgent' => 'sVersionMappingAgent', 
			'ArrayOfVersionMappingBackend' => 'sArrayOfVersionMappingBackend', 
			'VersionMappingBackend' => 'sVersionMappingBackend', 
			'RpLink' => 'sRpLink', 
			'ArrayOfRpLinks' => 'sArrayOfRpLinks', 
			'ArrayOfBackends' => 'sArrayOfBackends');
	}
}

class sFfNode{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $latitude;
	
	/**
	 * @var string
	 */
	public $longitude;
	
	/**
	 * @var string
	 */
	public $misc;
	
	/**
	 * @var string
	 */
	public $updateIntervalNode;
	
	/**
	 * @var string
	 */
	public $updateIntervalLink;
	
	/**
	 * @var string
	 */
	public $timeout;
	
	/**
	 * @var string
	 */
	public $hostname;
	
	/**
	 * @var string
	 */
	public $height;
	
	/**
	 * @var string
	 */
	public $isHna;
	
	/**
	 * @var string
	 */
	public $defGateway;
	
	/**
	 * @var string
	 */
	public $agentRelease;
	
	/**
	 * @var string
	 */
	public $upgradeTree;
	
	/**
	 * @var string
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $isGlobalUpdated;
	
	/**
	 * @var string
	 */
	public $replicatedBy;
	
	/**
	 * @var string
	 */
	public $isDummy;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
}

class sArrayOfFfNodes{
	
	/**
	 * @var array sFfNodes
	 */
	public $ffNodes = array();
}

class sBackend{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 */
	public $updatedAt;
}

class sAgentRelease{
	
	/**
	 * @var string
	 */
	public $release;
	
	/**
	 * @var string
	 */
	public $subRelease;
	
	/**
	 * @var string
	 */
	public $tree;
	
	/**
	 * @var string
	 */
	public $url;
	
	/**
	 * @var string
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $releaseDate;
}

class sVersionMappingAgent{
	
	/**
	 * @var string
	 */
	public $agentRelease;
	
	/**
	 * @var string
	 */
	public $agentSubRelease;
	
	/**
	 * @var string
	 */
	public $agentTree;
	
	/**
	 * @var string
	 */
	public $agentVersion;
	
	/**
	 * @var string
	 */
	public $backendRelease;
}

class sVersionMappingBackend{
	
	/**
	 * @var string
	 */
	public $serverRelease;
	
	/**
	 * @var string
	 */
	public $clientRelease;
}

class sArrayOfVersionMappingAgent{
	
	/**
	 * @var array sVersionMappingAgent
	 */
	public $ArrayOfVersionMappingAgent = array();
}

class sArrayOfVersionMappingBackend{
	
	/**
	 * @var array sVersionMappingBackend
	 */
	public $ArrayOfVersionMappingBackend = array();
}

class sArrayOfBackends{
	
	/**
	 * @var array sBackend
	 */
	public $backends = array();
}

class sArrayOfAgentRelease{
	
	/**
	 * @var array sAgentRelease
	 */
	public $agentRelease = array();
}

class sRpLink{
	
	/**
	 * @var string
	 */
	public $cost;
	
	/**
	 * @var string
	 */
	public $rx;
	
	/**
	 * @var string
	 */
	public $tx;
	
	/**
	 * @var string
	 */
	public $rp;
	
	/**
	 * @var string
	 */
	public $metric;
	
	/**
	 * @var string
	 */
	public $sourceNodeID;
	
	/**
	 * @var string
	 */
	public $sourceLat;
	
	/**
	 * @var string
	 */
	public $sourceLon;
	
	/**
	 * @var string
	 */
	public $destNodeID;
	
	/**
	 * @var string
	 */
	public $destLat;
	
	/**
	 * @var string
	 */
	public $destLon;
}

class sArrayOfRpLinks{
	
	/**
	 * @var array sRfLink
	 */
	public $arrayOfRpLinks = array();
}
?>