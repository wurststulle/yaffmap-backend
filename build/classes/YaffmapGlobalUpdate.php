<?php
class YaffmapGlobalUpdate extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
//		$allowed = array();
//		$this->checkInput($allowed);
	}
	
	public function globalUpdate(){
		if(isset($this->request['node'])){
			$request = Yaffmap::decodeJson($this->request['node']);
			$globalUpdateInterval = $this->request['updateIntervalGlobal'];
			foreach($request as $item){
				$firstIp = null;
				$node = null;
				$wlIfaces = array();
				foreach($item->iface as $if){
					if(isset($if->ipv4Addr)){
						// ipv4 address given
						if(!AddrMap::isValidIpv4Addr($if->ipv4Addr)){
							throw new EInvalidIpAddr($if->ipv4Addr);
						}
						if($firstIp == null){
							// save first ip to use it as hostname when no hostname is given
							$firstIp = $if->ipv4Addr;
						}
						$node = AddrMapNodeQuery::create()->filterByIpv4addr($if->ipv4Addr)->findOne();
						if($node == null){
							// node does not exist
							// create new addrMap and wl interface
							$addrMap = new AddrMap();
							$addrMap->setId(md5(mt_rand(1, 100000).$if->ipv4Addr.date('U')));
							$addrMap->setIpv4addr($if->ipv4Addr);
							$addrMap->setIsGlobalUpdated(true);
							$addrMap->save();
							$wlIf = new WlIface();
							$wlIf->setId(md5(mt_rand(1, 100000).$if->ipv4Addr.date('U')));
							$wlIf->setIsDummy(1);
							$wlIf->setAddrMap($addrMap);
							// link address map to wl interface and store wl interface in $wlIfaces collection
							$wlIfaces[] = $wlIf->copy();
						}
					}else{
						// ipv6 address given
						if(!AddrMap::isValidIpv6Addr($if->ipv6Addr)){
							throw new EInvalidIpAddr($if->ipv6Addr);
						}
						if($firstIp != null){
							// save first ip to use it as hostname when no hostname is given
							$firstIp = $if->ipv6Addr;
						}
						$node = AddrMapNodeQuery::create()->filterByIpv6addr($if->ipv6Addr)->findOne();
						if($node == null){
							// node does not exist
							// create new addrMap and wl interface
							$addrMap = new AddrMap();
							$addrMap->setId(md5(mt_rand(1, 100000).$if->ipv4Addr.date('U')));
							$addrMap->setIpv6addr($if->ipv6Addr);
							$addrMap->setIsGlobalUpdated(true);
							$addrMap->save();
							$wlIf = new WlIface();
							$wlIf->setId(md5(mt_rand(1, 100000).$if->ipv6Addr.date('U')));
							$wlIf->setIsDummy(1);
							$wlIf->setAddrMap($addrMap);
							// link address map to wl interface and store wl inerface in $wlIfaces collection
							$wlIfaces[] = $wlIf->copy();
						}
					}
				}
				if($node == null){
					// could not match any ip to a node, create new dummy node
					$node = new FfNode();
					if($item->name == ""){
						// no hostname given, use first ip
						$node->setHostname($firstIp);
					}else{
						$node->setHostname($item->name);
					}
					$node->setLatitude($item->latitude);
					$node->setLongitude($item->longitude);
					$node->setDefGateway($item->defGateway);
					$node->setIsHna((($item->isHna == 'true')?true:false));
					// add random nodeID
					$node->setId(md5($item->latitude.$item->longitude.mt_rand(1, 1000).$item->name.date('U')));
					$node->setIsGlobalUpdated(true);
					$node->setUpdateIntervalNode($globalUpdateInterval);
					$node->save();
					$wlDevice = WlDevice::createDummy($node);
					// add all items of $wlIfaces to this wlDevice
					foreach($wlIfaces as $wlIface){
						$wlIface->setWlDevice($wlDevice);
						$wlIface->save();
					}
				}else{
					// node was found, add all items of addrMaps to this node
					// ifaces are added to a random device
					$ffNode = $node->getFfNode();
					// update existing (dummy) node
					if($item->name == ""){
						// no hostname given, use first ip
						$ffNode->setHostname($firstIp);
					}else{
						$ffNode->setHostname($item->name);
					}
					if(!($ffNode->getLatitude() != null && $item->latitude == null || $ffNode->getLongitude() != null && $item->longitude == null)){
						// if lat/lon is empty in request, but already in database, do nothing, else do...
						$ffNode->setLatitude($item->latitude);
						$ffNode->setLongitude($item->longitude);
					}
					$ffNode->setDefGateway($item->defGateway);
					$ffNode->setIsHna((($item->isHna == 'true')?true:false));
					if($ffNode->getIsGlobalUpdated()){
						// update global update interval of global updated nodes
						$ffNode->setUpdateIntervalNode($globalUpdateInterval);
					}
					$ffNode->save();
					$wlDevice = $ffNode->getWlDevices()->getFirst();
					foreach($wlIfaces as $wlIface){
						$wlIface->setWlDevice($wlDevice);
						$wlIface->save();
					}
				}
			}
		}
		if(isset($this->request['link'])){
			$request = Yaffmap::decodeJson($this->request['link']);
			foreach($request->link as $link){
				if($request->ipv == 4){
					if(!AddrMap::isValidIpv4Addr($link->sAddr)){
						throw new EInvalidIpAddr($link->sAddr);
					}
					if(!AddrMap::isValidIpv4Addr($link->dAddr)){
						throw new EInvalidIpAddr($link->dAddr);
					}
					// ipv4 source address given
					$sAddr = AddrMapQuery::create()->filterByIpv4addr($link->sAddr)->findOneOrCreate();
					$dAddr = AddrMapQuery::create()->filterByIpv4addr($link->dAddr)->findOneOrCreate();
				}elseif($request->ipv == 0){
					// mac address given
					$sAddr = AddrMapQuery::create()->filterByMacAddr($link->sAddr)->findOneOrCreate();
					$dAddr = AddrMapQuery::create()->filterByMacAddr($link->dAddr)->findOneOrCreate();
				}else{
					// ipv6 address given
					$sAddr = AddrMapQuery::create()->filterByIpv6addr($link->sAddr)->findOneOrCreate();
					$dAddr = AddrMapQuery::create()->filterByIpv6addr($link->dAddr)->findOneOrCreate();
				}
				if($sAddr->isNew() && $dAddr->isNew()){
					// source and dest not found
					$sourceNode = FfNode::createDummy($link->sAddr);
					$sourceWlDevice = WlDevice::createDummy($sourceNode);
					$sAddr->setIsGlobalUpdated(true);
					$sAddr->save();
					$sourceWlIf = WlIface::createDummy($sourceWlDevice, $sAddr);
					$destNode = FfNode::createDummy($link->dAddr);
					$destWlDevice = WlDevice::createDummy($destNode);
					$dAddr->setIsGlobalUpdated(true);
					$dAddr->save();
					WlIface::createDummy($destWlDevice, $dAddr);
				}elseif(!($sAddr->isNew()) && $dAddr->isNew()){
					// dest address not in database
					if(!$sAddr->getIsGlobalUpdated()){
						// source was updated by node, skip global updating this link
						continue;
					}
					$destNode = FfNode::createDummy($link->dAddr);
					$destWlDevice = WlDevice::createDummy($destNode);
					$dAddr->setIsGlobalUpdated(true);
					$dAddr->save();
					$destWlIf = WlIface::createDummy($destWlDevice, $dAddr);
				}elseif($sAddr->isNew() && !($dAddr->isNew())){
					// source address not in database
					if(!$dAddr->getIsGlobalUpdated()){
						// destination was updated by node, skip global updating this link
						continue;
					}
					$sourceNode = FfNode::createDummy($link->sAddr);
					$sourceWlDevice = WlDevice::createDummy($sourceNode);
					$sAddr->setIsGlobalUpdated(true);
					$sAddr->save();
					WlIface::createDummy($sourceWlDevice, $sAddr);
				}else{
					if(!($sAddr->getIsGlobalUpdated()) || !($dAddr->getIsGlobalUpdated())){
						// if source or destination address is not global updated, skip
						continue;
					}
				}
				$metricType = MetricTypeQuery::create()->filterByName($request->metricType)->findOneOrCreate();
				if($metricType->isNew()){
					$metricType->save();
				}
				$rpType = RpQuery::create()
					->filterByName($request->rp)
					->filterByMetricType($metricType)
					->filterByIpv($request->ipv)
					->findOneOrCreate();
				if($rpType->isNew()){
					$rpType->save();
				}
				$rp = RpLinkQuery::create()->filterBySourceAddrMap($sAddr)->filterByDestAddrMap($dAddr)->findOneOrCreate();
				$rp->setRx($link->rx);
				$rp->setTx($link->tx);
				$rp->setCost($link->cost);
				$rp->setRp($rpType);
				$rp->setIsGlobalUpdated(true);
				$rp->save();
			}
		}
		$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
		$this->response->setResponseMsg('Operation Succeded.');
	}
}
?>