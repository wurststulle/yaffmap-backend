<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';

function echoPre($var){
	echo '<pre>'.$var.'</pre><br>------------------------------------------<br>';
}

$node = FfNodeQuery::create()->filterById($_REQUEST['id'])->findOne();
if($node == null){
	echo "node not found.";
	die();
}
$wlDevices = $node->getWlDevices();
$wiredIfaces = $node->getWiredIfaces();

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
$tr1 = $table->addThRow(array('Hostname', 'Latitude', 'Longitude', 'IsHna', 'DefGateway'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getHostname(), $node->getLatitude(), $node->getLongitude(), $node->getIsHna(), $node->getDefGateway()));

$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('wlDevices');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
	$table1 = new KTable();
	$table1->addAttribute(array(new KAttribute('border', 1)));
	$tr1 = $table1->addThRow(array('Name', 'Txpower', 'Channel', 'WirelessStandard', 'Frequency'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	foreach($wlDevices as $wlDevice){
		$table1->addRow(array($wlDevice->getName(), $wlDevice->getTxpower(), $wlDevice->getChannel(), $wlDevice->getWirelessStandard(), $wlDevice->getFrequency()));
		$td1 = new KTd();
		$td1->addAttribute(array(new KAttributeColspan('5')));
		$wlIfaces = $wlDevice->getWlIfaces();
		$table2 = new KTable();
		$table2->addAttribute(array(new KAttribute('border', 1)));
		$tr2 = $table2->addThRow(array('WlMacAddr', 'Name', 'WlMode', 'Bssid', 'Essid', 'BridgeName'));
		$tr2->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
		foreach($wlIfaces as $wlIface){
			$table2->addRow(array($wlIface->getWlMacAddr(), $wlIface->getName(), $wlIface->getWlMode(), $wlIface->getBssid(), $wlIface->getEssid(), $wlIface->getBridgeName()));
			
		}
		$td1->addItem($table2);
		$table1->addRow(array($td1));
	}






$td->addItem($table1);
$tr1 = $table->addRow(array($td));

$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('wiredDevices');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('wiredDevices');
$tr1 = $table->addRow(array($td));

$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('rpLinks');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('rpLinks');
$tr1 = $table->addRow(array($td));

$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('rfLinks');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('rfLinks');
$tr1 = $table->addRow(array($td));

echo $table;




$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
$tr1 = $table->addThRow(array('node'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$tr1 = $table->addThRow(array('Id', 'Latitude', 'Longitude'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getId(), $node->getLatitude(), $node->getLongitude()));
$tr1 = $table->addThRow(array('UpdateIntervalNode', 'UpdateIntervalLink', 'Timeout'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getUpdateIntervalNode(), $node->getUpdateIntervalLink(), $node->getTimeout()));
$tr1 = $table->addThRow(array('Hostname', 'Height', 'IsHna'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getHostname(), $node->getHeight(), $node->getIsHna()));
$tr1 = $table->addThRow(array('DefGateway', 'AgentRelease', 'UpgradeTree'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getDefGateway(), $node->getAgentRelease(), $node->getUpgradeTree()));
$tr1 = $table->addThRow(array('Version', 'IsGlobalUpdated', 'IsDummy'));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$table->addRow(array($node->getVersion(), $node->getIsGlobalUpdated(), $node->getIsDummy()));
echo $table;

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('8')));
$td->addItem('wlDevices');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$i = 0;
foreach($wlDevices as $wlDevice){
	$wlIfaces = $wlDevice->getWlIfaces();
	$numWlIfeces = $wlIfaces->count();
	$k = 0;
	foreach($wlIfaces as $wlIface){
		$addrMaps = $wlIface->getAddrMap();
		$ipAliase = $addrMaps->getIpAliass();
		$numIpAliasglobal[$k] = $ipAliase->count() *2;
		$k++;
	}
	$td1 = new KTd();
	if($numIpAliasglobal[$i] != 0) $numIpAliasglobal[$i] ++;
	$td1->addAttribute(array(new KAttribute('rowspan', $numIpAliasglobal[$i]+9)));
	$td1->addItem($i);
	$tr1 = $table->addThRow(array($td1, 'Id', 'Name', 'Txpower', 'Antdirection', 'Antbeamh', 'Antbeamv', 'IsDummy'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$table->addRow(array($wlDevice->getId(), $wlDevice->getName(), $wlDevice->getTxpower(), $wlDevice->getAntdirection(), $wlDevice->getAntbeamh(), $wlDevice->getAntbeamv(), $wlDevice->getIsDummy()));
	$tr1 = $table->addThRow(array('Antgain', 'Anttilt', 'Antpol', 'Channel', 'WirelessStandard', 'Frequency', 'Availfrequency'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$table->addRow(array($wlDevice->getAntgain(), $wlDevice->getAnttilt(), $wlDevice->getAntpol(), $wlDevice->getChannel(), $wlDevice->getWirelessStandard(), $wlDevice->getFrequency(), $wlDevice->getAvailfrequency()));
	$tr1 = $table->addThRow(array('wlIfaces'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$l = 0;
	foreach($wlIfaces as $wlIface){
		$addrMaps = $wlIface->getAddrMap();
		$ipAliase = $addrMaps->getIpAliass();
		$numIpAlias = $ipAliase->count() *2;
		if($numIpAlias != 0) $numIpAlias++;
		$td1 = new KTd();
		$td1->addAttribute(array(new KAttribute('rowspan', $numIpAlias + 4)));
		$td1->addItem($l);
		$tr1 = $table->addThRow(array($td1, 'Id', 'WlDeviceID', 'AddrMapID', 'WlMacAddr', 'Name', 'WlMode', 'Bssid', 'Essid', 'BridgeName', 'IsDummy'));
		$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
		$table->addRow(array($wlIface->getId(), $wlIface->getWlDeviceID(), $wlIface->getAddrMapID(), $wlIface->getWlMacAddr(), $wlIface->getName(), $wlIface->getWlMode(), $wlIface->getBssid(), $wlIface->getEssid(), $wlIface->getBridgeName(), $wlIface->getIsDummy()));
		$tr1 = $table->addThRow(array('Id', 'Ipv4addr', 'Ipv6addr', 'MacAddr', 'BridgeName', 'IsGlobalUpdated'));
		$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
		$table->addRow(array($addrMaps->getId(), $addrMaps->getIpv4addr(), $addrMaps->getIpv6addr(), $addrMaps->getMacAddr(), $addrMaps->getBridgeName(), $addrMaps->getIsGlobalUpdated()));
		$j = 0;
		if($numIpAlias != 0){
			$tr1 = $table->addThRow(array('ipAlias'));
			$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
		}
		foreach($ipAliase as $ipAlias){
			$td1 = new KTd();
			$td1->addAttribute(array(new KAttribute('rowspan', '2')));
			$td1->addItem($j);
			$tr1 = $table->addThRow(array($td1, 'Ipv4addr', 'Ipv6addr', 'AddrMapID', 'Name'));
			$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
			$table->addRow(array($ipAlias->getIpv4addr(), $ipAlias->getIpv6addr(), $ipAlias->getAddrMapID(), $ipAlias->getName()));
			$j++;
		}
		$l++;
	}
	$i++;
}
echo $table;

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
$td = new KTd();
$td->addAttribute(array(new KAttributeColspan('5')));
$td->addItem('wiredDevices');
$tr1 = $table->addThRow(array($td));
$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
$i = 0;
foreach($wiredIfaces as $wiredIface){
	$addrMaps = $wiredIface->getAddrMap();
	$ipAliase = $addrMaps->getIpAliass();
	$numIpAlias = $ipAliase->count();
	if($numIpAlias != 0) $numIpAlias++;
	$td1 = new KTd();
	$td1->addAttribute(array(new KAttribute('rowspan',  $numIpAlias*2 + 4)));
	$td1->addItem($i);
	$tr1 = $table->addThRow(array($td1, 'AddrMapID', 'Name', 'BridgeName', 'IsDummy'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$table->addRow(array($wiredIface->getAddrMapID(), $wiredIface->getName(), $wiredIface->getBridgeName(), $wiredIface->getIsDummy()));
	$tr1 = $table->addThRow(array('Id', 'Ipv4addr', 'Ipv6addr', 'MacAddr', 'BridgeName', 'IsGlobalUpdated'));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$table->addRow(array($addrMaps->getId(), $addrMaps->getIpv4addr(), $addrMaps->getIpv6addr(), $addrMaps->getMacAddr(), $addrMaps->getBridgeName(), $addrMaps->getIsGlobalUpdated()));
	$j = 0;
	if($numIpAlias != 0){
		$tr1 = $table->addThRow(array('ipAlias'));
		$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	}
	foreach($ipAliase as $ipAlias){
		$td1 = new KTd();
		$td1->addAttribute(array(new KAttribute('rowspan', '2')));
		$td1->addItem($j);
		$tr1 = $table->addThRow(array($td1, 'Ipv4addr', 'Ipv6addr', 'AddrMapID', 'Name'));
		$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
		$table->addRow(array($ipAlias->getIpv4addr(), $ipAlias->getIpv6addr(), $ipAlias->getAddrMapID(), $ipAlias->getName()));
		$j++;
	}
	$i++;
}
echo $table;
?>