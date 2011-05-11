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
			'ArrayOfBackends' => 'sArrayOfBackends',
			'ArrayOfWiredIfaces' => 'sArrayOfWiredIfaces',
			'WiredIface' => 'sWiredIface',
			'AddrMap' => 'sAddrMap',
			'IpAlias' => 'sIpAlias',
			'WlDevice' => 'sWlDevice',
			'WlIface' => 'sWlIface');
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
	 * one of the nodes addresses(mac or ipv4/6), used to identify the node while replication
	 * 
	 * @var string
	 */
	public $addr;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
	
	/**
	 * @var array sWiredIfaces
	 */
	public $wiredIfaces;
	
	/**
	 * @var array sWlDevices
	 */
	public $wlDevices;
}

class sArrayOfWiredIfaces{
	
	/**
	 * @var array sWiredIface
	 */
	public $wiredIfaces = array();
}

class sArrayOfWlDevices{
	
	/**
	 * @var array sWlDevice
	 */
	public $wlDevices = array();
}

class sWlDevice{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $txpower;
	
	/**
	 * @var string
	 */
	public $antDirection;
	
	/**
	 * @var string
	 */
	public $antBeamH;
	
	/**
	 * @var string
	 */
	public $antBeamV;
	
	/**
	 * @var string
	 */
	public $antGain;
	
	/**
	 * @var string
	 */
	public $antTilt;
	
	/**
	 * @var string
	 */
	public $antPol;
	
	/**
	 * @var string
	 */
	public $channel;
	
	/**
	 * @var string
	 */
	public $wirelessStandard;
	
	/**
	 * @var string
	 */
	public $frequency;
	
	/**
	 * @var string
	 */
	public $availFrequency;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
	
	/**
	 * @var array sWlIface
	 */
	public $wlIfaces;
}

class sWlIface{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var array sWlIface
	 */
	public $wlMacAddr;
	
	/**
	 * @var array sWlIface
	 */
	public $name;
	
	/**
	 * @var array sWlIface
	 */
	public $wlMode;
	
	/**
	 * @var array sWlIface
	 */
	public $bssid;
	
	/**
	 * @var array sWlIface
	 */
	public $essid;
	
	/**
	 * @var array sWlIface
	 */
	public $bridgeName;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
	
	/**
	 * @var object sAddrMap
	 */
	public $addrMap;
}

class sWiredIface{
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $bridgeName;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
	
	/**
	 * @var object sAddrMap
	 */
	public $addrMap;
}

class sAddrMap{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $ipv4Addr;
	
	/**
	 * @var string
	 */
	public $ipv6Addr;
	
	/**
	 * @var string
	 */
	public $macAddr;
	
	/**
	 * @var string
	 */
	public $bridgeName;
	
	/**
	 * @var string
	 */
	public $isGlobalUpdated;
	
	/**
	 * @var string
	 */
	public $createdAt;
	
	/**
	 * @var string
	 */
	public $updatedAt;
	
	/**
	 * @var array sIpAlias
	 */
	public $ipAlias;
}

class sIpAlias{
	
	/**
	 * @var string
	 */
	public $id;
	
	/**
	 * @var string
	 */
	public $ipv4Addr;
	
	/**
	 * @var string
	 */
	public $ipv6Addr;
	
	/**
	 * @var string
	 */
	public $name;
	
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
	 * @var array sFfNode
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