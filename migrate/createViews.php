<?php
require_once '../build/propel/Propel.php';
Propel::init('../build/conf/yaffmap-conf.php');
set_include_path("../build/classes".PATH_SEPARATOR.get_include_path());

$con = Propel::getConnection(ConfigPeer::DATABASE_NAME);
$sql = "create view yaffmap_v_addrMapNode as (
		select t4.ID_node, t1.ID_addrMap, t1.ipv4Addr, t1.ipv6Addr, t1.macAddr, t4.latitude, t4.longitude, 'wireless' as type 
		FROM yaffmap_addrMap t1
		inner join yaffmap_wlIface t2 on t1.ID_addrMap = t2.f_addrMapID 
		inner join yaffmap_wlDevice t3 on t2.f_wlDeviceID = t3.ID_wlDevice
		inner join yaffmap_node t4 on t3.f_nodeID = t4.ID_node
	) union (
		select t3.ID_node, t1.ID_addrMap, t1.ipv4Addr, t1.ipv6Addr, t1.macAddr, t3.latitude, t3.longitude, 'wired' as type 
		FROM yaffmap_addrMap t1
		inner join yaffmap_wiredIface t2 on t1.ID_addrMap = t2.f_addrMapID 
		inner join yaffmap_node t3 on t2.f_nodeID = t3.ID_node
	) union (
		select t4.ID_node, t1.ID_addrMap, t5.ipv4Addr, t5.ipv6Addr, t1.macAddr, t4.latitude, t4.longitude, 'wirelessAlias' as type 
		FROM yaffmap_addrMap t1
		inner join yaffmap_wlIface t2 on t1.ID_addrMap = t2.f_addrMapID 
		inner join yaffmap_wlDevice t3 on t2.f_wlDeviceID = t3.ID_wlDevice
		inner join yaffmap_node t4 on t3.f_nodeID = t4.ID_node
		inner join yaffmap_ipAlias t5 on t1.ID_addrMap = t5.f_addrMapID
	) union (
		select t3.ID_node, t1.ID_addrMap, t4.ipv4Addr, t4.ipv6Addr, t1.macAddr, t3.latitude, t3.longitude, 'wiredAlias' as type 
		FROM yaffmap_addrMap t1
		inner join yaffmap_wiredIface t2 on t1.ID_addrMap = t2.f_addrMapID 
		inner join yaffmap_node t3 on t2.f_nodeID = t3.ID_node
		inner join yaffmap_ipAlias t4 on t1.ID_addrMap = t4.f_addrMapID
	)";
$stmt = $con->prepare($sql);
$stmt->execute();
$con = Propel::getConnection(ConfigPeer::DATABASE_NAME);
$sql = "create view yaffmap_v_rpLinkLocation as (
		SELECT t1.cost, t1.rx, t1.tx, t4.name as rp, t5.name as metric, t2.ID_node as sourceNodeID, t2.latitude as sourceLat, t2.longitude as sourceLon, t3.ID_node as destNodeID, t3.latitude as destLat, t3.longitude as destLon FROM yaffmap_rpLink t1
		inner join yaffmap_v_addrMapNode t2 on t1.f_sourceAddrMapID = t2.ID_addrMap
		inner join yaffmap_v_addrMapNode t3 on t1.f_destAddrMapID = t3.ID_addrMap
		inner join yaffmap_rp t4 on t1.f_rpID = t4.ID_rp
		inner join yaffmap_metricType t5 on t4.f_metricID = t5.ID_metricType
		WHERE t2.latitude IS NOT NULL AND t2.longitude IS NOT NULL AND t3.latitude IS NOT NULL AND t3.longitude IS NOT NULL
		GROUP BY sourceNodeID, destNodeID
	)";
$stmt = $con->prepare($sql);
$stmt->execute();
$con = Propel::getConnection(ConfigPeer::DATABASE_NAME);
$sql = "create view yaffmap_v_unlocatedNodes as (
		select t1.ID_node, t1.hostname, t1.misc, t4.ipv4Addr as ipAddr, 'ipv4' as addrType from yaffmap_node t1
		inner join yaffmap_wlDevice t2 on t2.f_nodeID = t1.ID_node
		inner join yaffmap_wlIface t3 on t2.ID_wlDevice = t3.f_wlDeviceID
		inner join yaffmap_addrMap t4 on t3.f_addrMapID = t4.ID_addrMap
		WHERE t4.ipv6Addr is null AND t1.latitude is null AND t1.longitude is null
		group by t1.ID_node
	)union(
		select t1.ID_node, t1.hostname, t1.misc, t4.ipv6Addr as ipAddr, 'ipv6' as addrType from yaffmap_node t1
		inner join yaffmap_wlDevice t2 on t2.f_nodeID = t1.ID_node
		inner join yaffmap_wlIface t3 on t2.ID_wlDevice = t3.f_wlDeviceID
		inner join yaffmap_addrMap t4 on t3.f_addrMapID = t4.ID_addrMap
		WHERE t4.ipv4Addr is null AND t1.latitude is null AND t1.longitude is null
		group by t1.ID_node
	)";
$stmt = $con->prepare($sql);
$stmt->execute();
?>