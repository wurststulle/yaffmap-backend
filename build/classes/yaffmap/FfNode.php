<?php



/**
 * Skeleton subclass for representing a row from the 'ffmap_node' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.ffmap
 */
class FfNode extends BaseFfNode {
	
	/**
	 * return ffNode by given id
	 * @param string $id
	 * @return FfNode
	 */
	public static function getNodeById($id){
		return FfNodeQuery::create()->findOneById($id);
	}
	
	public static function findOneByAddr($addr, $hostname, $dbCon = null){
		if(AddrMap::isValidIpv4Addr($addr)){
			$addrMap = AddrMapNodeQuery::create()->filterByIpv4addr($addr)->filterByHostname($hostname)->findOne($dbCon);
			/* @var $addrMap AddrMapNode */
			if($addrMap != null){
				return $addrMap->getFfNode();
			}
		}elseif(AddrMap::isValidIpv6Addr($addr)){
			$addrMap = AddrMapNodeQuery::create()->filterByIpv6addr($addr)->filterByHostname($hostname)->findOne($dbCon);
			/* @var $addrMap AddrMapNode */
			if($addrMap != null){
				return $addrMap->getFfNode();
			}
		}else{
			$addrMap = AddrMapNodeQuery::create()->filterByMacAddr($addr)->filterByHostname($hostname)->findOne($dbCon);
			/* @var $addrMap AddrMapNode */
			if($addrMap != null){
				return $addrMap->getFfNode();
			}
		}
		return null;
	}
	
	/**
	 * update attributes of ffNode with given update
	 * @param unknown_type $update
	 * @throws EUnknownAttribute
	 */
	public function updateNode($update, $version, $tree, $release){
		$this->setAgentRelease($release);
		$this->setVersion($version);
		$this->setUpgradeTree($tree);
		foreach(get_object_vars($update) as $key => $val){
			if(!(is_array($val) || $key == 'id' || is_object($val))){
				if($key == 'isHna'){
					if($val == 'true'){
						$this->setIsHna(true);
					}else{
						$this->setIsHna(false);
					}
					continue;
				}
				try{
					call_user_func_array(array($this, 'set'.ucfirst($key)), array($val));
				}catch(Exception $e){
					throw new EUnknownAttribute($key);
				}
			}
		}
	}
	
	/**
	 * delete all AddrMaps of ffNode
	 */
	public function deleteAddrMaps(){
		$guWlDevices = $this->getWlDevices();
		foreach($guWlDevices as $guDevice){
			$guWlIfaces = $guDevice->getWlIfaces();
			foreach($guWlIfaces as $guWlIface){
				$guWlIface->getAddrMap()->delete();
			}
		}
	}
	
	public function updateRpLink($request){
		foreach($request as $rpName => $rpAttr){
			$metricType = MetricTypeQuery::create()
				->filterByName($rpAttr->metric)
				->findOneOrCreate();
			$metricType->save();
			$rp = RpQuery::create()
				->filterByName($rpName)
				->filterByIpv($rpAttr->ipv)
				->filterByMetricType($metricType)
				->findOneOrCreate();
			$rp->save();
			foreach($rpAttr->link as $link){
				if($rp->getIpv() == 4){
					// ipv4 address given
					$source = AddrMapQuery::create()
						->filterByIpv4addr($link->sourceAddr)
						->findOne();
					$dest = AddrMapQuery::create()
						->filterByIpv4addr($link->destAddr)
						->findOneOrCreate();
				}elseif($rp->getIpv() == 6){
					// ipv6 address given
					$source = AddrMapQuery::create()
						->filterByIpv6addr($link->sourceAddr)
						->findOne();
					$dest = AddrMapQuery::create()
						->filterByIpv6addr($link->destAddr)
						->findOneOrCreate();
				}else{
					// mac address given (layer2 rp)
					$source = AddrMapQuery::create()
						->filterByMacAddr($link->sourceAddr)
						->findOne();
					$dest = AddrMapQuery::create()
						->filterByMacAddr($link->destAddr)
						->findOneOrCreate();
				}
				if($dest->isNew()){
					// destination address map not found, create dummy node + device + interface
					$dest->setIsGlobalUpdated(true);
					$dest->save();
					$node = new FfNode();
					$node->setHostname($link->destAddr);
					$node->setId(md5($link->destAddr.date('U')));
					$node->setIsGlobalUpdated(true);
					$node->save();
					$wlDevice = new WlDevice();
					$wlDevice->setFfNode($node);
					$wlDevice->save();
					$wlIface = new WlIface();
					$wlIface->setWlDevice($wlDevice);
					$wlIface->setAddrMap($dest);
					$wlIface->save();
				}
				if($source == null || $dest == null){
					// source or destination not found, skipping
					continue;
				}
				$source->setUpdatedAt(new DateTime("now"));
				$rplink = RpLinkQuery::create()
					->filterBySourceAddrMap($source)
					->filterByDestAddrMap($dest)
					->filterByRp($rp)
					->findOneOrCreate();
				$rplink->setUpdatedAt(new DateTime("now"));
				foreach($link as $key => $val){
					if(!($key == 'sourceAddr' || $key == 'destAddr')){
						try{
							call_user_func_array(array($rplink, 'set'.ucfirst($key)), array($val));
						}catch(Exception $e){
							throw new EUnknownAttribute($key);
						}
					}
				}
				$rplink->save();
			}
		}
	}

	public function updateRfLink($request){
		foreach($request as $key => $rflink){
			$sourceWlIface = WlIfaceQuery::create()
				->filterByWlMacAddr($rflink->sMac)
				->useWlDeviceQuery()
					->filterByFfNode($this)
				->endUse()
				->findOne();
			$destWlIface = WlIfaceQuery::create()
				->filterByWlMacAddr($rflink->dMac)
				->find();
			if($sourceWlIface == null){
//				$error = new ErrorLog();
//				$error->setRequest($_REQUEST['do']);
//				$error->setRequeststring(Kobold::dump_ret($_REQUEST));
//				$error->setError('source wireless interface ('.$rflink->sMac.') not found while updating rfLinks in node update, skip query.');
//				$error->setIp($_SERVER['REMOTE_ADDR']);
//				$error->setType('warning');
//				$error->save();
				continue;
//				throw new Exception('quelle leer, ahhhhh! '.$rflink->sMac);
			}
			if($destWlIface->count() > 1){
				// destination wireless interface duplicate entry found, skipping
				continue;
			}elseif($destWlIface->count() == 0){
				// destination wireless interface not in database, create one-way rf link
				$rf = RfLinkOneWayQuery::create()
					->filterBySourceWlIfaceOneWay($sourceWlIface)
					->filterByDestMac($rflink->dMac)
					->findOneOrCreate();
			}else{
				// create normal rf link
				$rf = RfLinkQuery::create()
					->filterBySourceWlIface($sourceWlIface)
					->filterByDestWlIface($destWlIface->getFirst())
					->findOneOrCreate();
			}
			$sourceWlIface->setUpdatedAt(new DateTime("now"));
			$rf->setUpdatedAt(new DateTime("now"));
			foreach($rflink as $key1 => $val1){
				if(!($key1 == 'sMac' || $key1 == 'dMac')){
					try{
						call_user_func_array(array($rf, 'set'.ucfirst($key1)), array($val1));
					}catch(Exception $e){
						throw new EUnknownAttribute($key1);
					}
				}
			}
			$rf->save();
		}
	}
	
	public function updateWlInterface($request){
		foreach($request as $wlDevices){
			// get or create wireless device
			$wlDevice = WlDeviceQuery::create()
				->filterByFfNode($this)
				->filterByName($wlDevices->name)
				->findOneOrCreate();
			// update timestamp and save
			$wlDevice->setUpdatedAt(new DateTime("now"));
			$wlDevice->save();
			// process wireless device attributes
			foreach($wlDevices as $key => $val){
				if($key == 'wlIface'){
					// handle wireless interfaces
					foreach($val as $wlIface){
						if(!isset($wlIface->bridgeName)){
							// wireless interface is NOT part of a bridge
							$addrMap = AddrMap::findOneAddrMapByAddr(null, $wlIface->ipv4Addr, $wlIface->ipv6Addr, false, true);
							if(!($addrMap->isNew()) && $addrMap->getIsGlobalUpdated()){
								// addrMap is not new AND global updated
								// get dummynode of addrMap
								$oldnode = FfNodeQuery::create()
									->useWlDeviceQuery()
										->useWlIfaceQuery()
											->filterByAddrMap($addrMap)
										->endUse()
									->endUse()
									->findOne();
								// move all wireless interface of this dummy node to the "real" node
								if($oldnode == null){
									throw new Exception('kein node mit addrMap found ->'.$wlIface->ipv4Addr.' addrMapID:'.$addrMap->getId().' ifaceID:'.$oldIface->getId().' deviceID:'.$oldDevice->getId());
								}
								$oldnode->deleteAddrMaps();
								$oldnode->delete();
								$addrMap = AddrMap::findOneAddrMapByAddr(null, $wlIface->ipv4Addr, $wlIface->ipv6Addr, false, true);
								$addrMap->save();
							}
							$wlInterface = WlIfaceQuery::create()
								->filterByWlDevice($wlDevice)
								->filterByWlMacAddr($wlIface->wlMacAddr)
								->findOneOrCreate();
							// update addrMap
							$addrMap->setMacAddr($wlIface->macAddr);
							$addrMap->setUpdatedAt(new DateTime("now"));
							$addrMap->setIsGlobalUpdated(false);
							$addrMap->save();
							foreach($wlIface as $key1 => $val1){
								if($key1 == 'ipAlias'){
									// process ip alias
									foreach($val1 as $ipAlias){
										$guAlias = IpAlias::findOneIpAlias(null, null, $ipAlias->ipv4Addr, $ipAlias->ipv6Addr);
										if($guAlias == null){
											// alias was not previously inserted into address map by global update
											$alias = IpAlias::findOneIpAlias($addrMap, $ipAlias->name, $ipAlias->ipv4Addr, $ipAlias->ipv6Addr, true);
										}else{
											// alias was previously inserted into address map by global update
											// create new alias and add it to address map
											$alias = new IpAlias();
											if(isset($ipAlias->ipv4Addr)){
												$alias->setIpv4addr($ipAlias->ipv4Addr);
											}else{
												$alias->setIpv6addr($ipAlias->ipv6Addr);
											}
											$alias->setAddrMap($addrMap);
											$alias->setName($ipAlias->name);
											// delete old address map incl interface
											$guAlias->delete();
										}
										// update ip alias timestamp and save
										$alias->setUpdatedAt(new DateTime("now"));
										$alias->save();
									}
								}elseif($key1 != 'ipv4Addr' && $key1 != 'ipv6Addr' && $key1 != 'macAddr'){
									// set or update wireless interface attributes
									try{
										call_user_func_array(array($wlInterface, 'set'.ucfirst($key1)), array($val1));
									}catch(Exception $e){
										throw new EUnknownAttribute($key1);
									}
								}
							}
							$wlInterface->setUpdatedAt(new DateTime("now"));
							$wlInterface->setAddrMap($addrMap);
							$wlInterface->save();
						}else{
							// wireless interface is part of a bridge
							$addrMap = AddrMap::findOneAddrMapByAddr(null, $wlIface->ipv4Addr, $wlIface->ipv6Addr, false, true);
							if(!($addrMap->isNew()) && $addrMap->getIsGlobalUpdated()){
								// addrMap is not new AND global updated
								// get dummynode of addrMap
								$oldnode = FfNodeQuery::create()
									->useWlDeviceQuery()
										->useWlIfaceQuery()
											->filterByAddrMap($addrMap)
										->endUse()
									->endUse()
									->findOne();
								// move all wireless interface of this dummy node to the "real" node
								if($oldnode == null){
									throw new Exception('kein node mit addrMap found ->'.$wlIface->ipv4Addr.' addrMapID:'.$addrMap->getId().' ifaceID:'.$oldIface->getId().' deviceID:'.$oldDevice->getId());
								}
								$guWlDevices = $oldnode->getWlDevices(); // get all wireless devices of dummy node
								foreach($guWlDevices as $guDevice){
									// delete all addrMaps of this node
									$guWlIfaces = $guDevice->getWlIfaces();
									foreach($guWlIfaces as $guWlIface){
										$guWlIface->getAddrMap()->delete();
									}
								}
								$oldnode->delete(); // delete dummy node
								$addrMap = AddrMap::findOneAddrMapByAddr($wlIface->macAddr, $wlIface->ipv4Addr, $wlIface->ipv6Addr, $wlIface->bridgeName, true);
							}else{
								$addrMap->setBridgeName($wlIface->bridgeName);
							}
							$wlInterface = WlIfaceQuery::create()
								->filterByWlDevice($wlDevice)
								->filterByWlMacAddr($wlIface->wlMacAddr)
								->filterByBridgeName($wlIface->bridgeName) //
								->findOneOrCreate();
							$wlInterface->setUpdatedAt(new DateTime("now"));
							if($addrMap->getBridgeName() != $wlIface->bridgeName){
								throw new Exception('bridge name does not match! '.$addrMap->getBridgeName().' => '.$wlIface->bridgeName);
							}
							$addrMap->setMacAddr($wlIface->macAddr);
							$addrMap->setUpdatedAt(new DateTime("now"));
							$addrMap->setIsGlobalUpdated(false);
							$addrMap->save();
							foreach($wlIface as $key1 => $val1){
								if($key1 == 'ipAlias'){
									foreach($val1 as $ipAlias){
										$alias = IpAlias::findOneIpAlias($addrMap, $ipAlias->name, $ipAlias->ipv4Addr, $ipAlias->ipv6Addr, true);
										$alias->setUpdatedAt(new DateTime("now"));
										$alias->setAddrMap($addrMap);
										$alias->save();
									}
								}elseif($key1 != 'ipv4Addr' && $key1 != 'ipv6Addr' && $key1 != 'macAddr'){
									// set or update wireless interface attributes
									try{
										call_user_func_array(array($wlInterface, 'set'.ucfirst($key1)), array($val1));
									}catch(Exception $e){
										throw new EUnknownAttribute($key1);
									}
								}
							}
							$wlInterface->setUpdatedAt(new DateTime("now"));
							$wlInterface->setAddrMap($addrMap);
							$wlInterface->save();
						}
						// recycle one-way rf links
						$rfOneWay = RfLinkOneWayQuery::create()
							->filterByDestMac($wlIface->wlMacAddr)
							->find();
						if($rfOneWay->count() != 0){
							// wlMacAddr of this wlinterface found in rfOneWay link(s)
							// create new rfLink and recycle rfOneWay data
							foreach($rfOneWay as $rfOneWayLink){
								$rflink = new RfLink();
								$rflink->setSourceWlIface($rfOneWayLink->getSourceWlIfaceOneWay());
								$rflink->setDestWlIface($wlInterface);
								$rflink->setRssi($rfOneWayLink->getRssi());
								$rflink->setTxrate($rfOneWayLink->getTxRate());
								$rflink->save();
								$rfOneWayLink->delete();
							}
						}
					}
				}else{
					// set or update attributes
					try{
						call_user_func_array(array($wlDevice, 'set'.ucfirst($key)), array($val));
					}catch(Exception $e){
						throw new EUnknownAttribute($key);
					}
				}
			}
			$wlDevice->save();
		}
	}
	
	public function updateWiredInterface($request){
		foreach($request as $item){
			if($item->name == 'lo'){
				// ignore loopback interface
				continue;
			}
			if((!isset($item->ipv4Addr) && !isset($item->ipv6Addr) && !isset($item->macAddr)) 
				|| ($item->ipv4Addr == '' && $item->ipv6Addr == '' && $item->macAddr == '')){
				// link with no source or destination address given, skipping
				continue;
			}
			if(!isset($item->bridgeName)){
				// interface is not in a bridge
				$wiredIface = WiredIfaceQuery::create()
					->filterByFfNode($this)
					->filterByName($item->name)
					->findOneOrCreate();
				if($wiredIface->isNew()){
					// wired interface was created, create new address map
					$addMap = new AddrMap();
					$addMap->setMacAddr($item->macAddr);
					$addMap->setIpv4addr($item->ipv4Addr);
					$addMap->setIpv6addr($item->ipv6Addr);
				}else{
					// wired interface found, update address map
					$addMap = AddrMap::findOneAddrMapByAddr($item->macAddr, $item->ipv4Addr, $item->ipv6Addr, false, true);
					$addMap->setUpdatedAt(new DateTime("now"));
				}
				$addMap->save();
				$wiredIface->setUpdatedAt(new DateTime("now"));
				$wiredIface->setAddrMap($addMap);
				$wiredIface->save();
				if(isset($item->ipAlias)){
					// ip address alias found, create or update them
					foreach($item->ipAlias as $ipAlias){
						$alias = IpAlias::findOneIpAlias($addMap, $ipAlias->name, $ipAlias->ipv4Addr, $ipAlias->ipv6Addr, true);
						$alias->setUpdatedAt(new DateTime("now"));
						$alias->save();
					}
				}
			}else{
				// interface is a bridge
				$addMap = AddrMap::findOneAddrMapByAddr($item->macAddr, $item->ipv4Addr, $item->ipv6Addr, $item->bridgeName, true);
				$addMap->setUpdatedAt(new DateTime("now"));
				$addMap->save();
				$wiredIface = WiredIfaceQuery::create()
					->filterByFfNode($this)
					->filterByName($item->name)
					->filterByBridgeName($item->bridgeName)
					->findOneOrCreate();
				$wiredIface->setUpdatedAt(new DateTime("now"));
				$wiredIface->setAddrMap($addMap);
				$wiredIface->save();
				if(isset($item->ipAlias)){
					// ip address alias found, create or update them
					foreach($item->ipAlias as $ipAlias){
						$alias = IpAlias::findOneIpAlias($addMap, $ipAlias->name, $ipAlias->ipv4Addr, $ipAlias->ipv6Addr, true);
						$alias->setUpdatedAt(new DateTime("now"));
						$alias->save();
					}
				}
			}
		}
	}

	public static function createDummy($hostname = null){
		$dummyNode = new FfNode();
		$dummyNode->setId(md5($hostname.mt_rand(1, 1000).$item->name.date('U')));
		$dummyNode->setHostname($hostname);
		$dummyNode->setIsDummy(1);
		$dummyNode->setIsGlobalUpdated(true);
		$dummyNode->save();
		return $dummyNode;
	}
	
	/**
	 * @return sFfNode
	 */
	public function getSoapClass(){
		$n = new sFfNode();
		$n->id = $this->getId();
		$n->latitude = $this->getLatitude();
		$n->longitude = $this->getLongitude();
		$n->misc = $this->getMisc();
		$n->updateIntervalNode = $this->getUpdateIntervalNode();
		$n->updateIntervalLink = $this->getUpdateIntervalLink();
		$n->timeout = $this->getTimeout();
		$n->hostname = $this->getHostname();
		$n->height = $this->getHeight();
		if($this->getIsHna()){
			$n->isHna = 'true';
		}else{
			$n->isHna = 'false';
		}
		$n->defGateway = $this->getDefGateway();
		$n->agentRelease = $this->getAgentRelease();
		$n->upgradeTree = $this->getUpgradeTree();
		$n->version = $this->getVersion();
		if($this->getIsGlobalUpdated()){
			$n->isGlobalUpdated = 'true';
		}else{
			$n->isGlobalUpdated = 'false';
		}
		$n->replicatedBy = $this->getReplicatedBy().'|'.YaffmapConfig::get('id');
		$n->isDummy = $this->getIsDummy();
		$n->createdAt = $this->getCreatedAt();
		$n->updatedAt = $this->getUpdatedAt();
		return $n;
	}
	
	/**
	 * @return FfNode
	 */
	public static function createOne($node){
		$localNode = new FfNode();
		$localNode->setId($node->id);
		$localNode->setAgentRelease((($node->agentRelease == '')?NULL:$node->agentRelease));
		$localNode->setCreatedAt($node->createdAt);
		$localNode->setDefGateway((($node->defGateway == '')?NULL:$node->defGateway));
		$localNode->setHeight((($node->height == '')?NULL:$node->height));
		$localNode->setHostname((($node->hostname == '')?NULL:$node->hostname));
		$localNode->setIsGlobalUpdated((($node->isGlobalUpdated == '')?NULL:$node->isGlobalUpdated));
		$localNode->setIsHna((($node->isHna == '')?NULL:$node->isHna));
		$localNode->setLatitude((($node->latitude == '')?NULL:$node->latitude));
		$localNode->setLongitude((($node->longitude == '')?NULL:$node->longitude));
		$localNode->setMisc((($node->misc == '')?NULL:$node->misc));
		$localNode->setReplicatedBy((($node->replicatedBy == '')?NULL:$node->replicatedBy));
		$localNode->setTimeout($node->timeout);
		$localNode->setUpdatedAt($node->updatedAt);
		$localNode->setUpgradeTree((($node->upgradeTree == '')?NULL:$node->upgradeTree));
		$localNode->setVersion((($node->version == '')?NULL:$node->version));
		$localNode->setUpdateIntervalNode((($node->updateIntervalNode == '')?NULL:$node->updateIntervalNode));
		$localNode->setUpdateIntervalLink((($node->updateIntervalLink == '')?NULL:$node->updateIntervalLink));
		return $localNode;
	}
} // FfNode
