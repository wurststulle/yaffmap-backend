<?php
ini_set("soap.wsdl_cache_enabled", "0");

class YaffmapBackend{
	
	/**
	 * @deprecated
	 */
	public function generateId(){
		return YaffmapConfig::get('id');
	}
	
	/**
	 * announce this backend to backend with given url, or to all known backends
	 * 
	 * @param string $url url of the backend announce should go to (optional)
	 * @throws YaffmapException
	 * @throws YaffmapSoapException
	 */
	public function announce($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			if(is_array($conns)){
				foreach($conns as $conn){
					$result = $conn->announceBackend(YaffmapConfig::get('id'), YaffmapConfig::get('url'), YaffmapConfig::get('version'));
					echo Kobold::dump($result);
				}
			}else{
				echo Kobold::dump('no other backend found.');
			}
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw new YaffmapException($e);
		}
	}
	
	/**
	 * get all backends from backend with given url, or all known backends
	 * 
	 * @param string $url
	 * @throws YaffmapException
	 * @throws YaffmapSoapException
	 */
	public function getBackends($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			$dbCon = Propel::getConnection(BackendsPeer::DATABASE_NAME);
			$dbCon->beginTransaction();
			$countBackendsAdded = 0;
			$countBackendsUpdated = 0;
			if(is_array($conns)){
				foreach($conns as $conn){
					$b = $conn->getBackends(YaffmapConfig::get('version'))->backends;
					if(!is_array($b)){
						// TODO remove workaround
						$backends[] = $b;
					}else{
						$backends = $b;
					}
					foreach($backends as $backend){
						$b = BackendsQuery::create()->filterById($backend->id)->findOneOrCreate($dbCon);
						if(!$b->isNew() && $backend->updatedAt > $b->getUpdatedAt()){
							// given backend is not new and younger
							$b->setUpdatedAt($backend->updatedAt);
							$b->save($dbCon);
							$countBackendsUpdated++;
						}elseif($b->isNew() && $b->getId() != YaffmapConfig::get('id')){
							// backend was not known before and not this backend
							$b->setUrl($backend->url);
							$b->setUpdatedAt($backend->updatedAt);
							$b->setCreatedAt($backend->updatedAt);
							$b->save($dbCon);
							$countBackendsAdded++;
						}
					}
				}
			}
			$dbCon->commit();
			return 'backends: added: '.$countBackendsAdded.', updated: '.$countBackendsUpdated;
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw new YaffmapException($e);
		}
	}
	
	/**
	 * get agent releases from backend with given url, or all known backends
	 * 
	 * @param string $url
	 * @throws YaffmapException
	 * @throws YaffmapSoapException
	 */
	public function getAgentRelease($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			if(is_array($conns)){
				$repplicatedAgents = 0;
				foreach($conns as $conn){
					$r = $conn->getAgentRelease(YaffmapConfig::get('version'));
					if(!is_array($r->agentRelease)){
						// TODO remove workaround
						$releases->agentRelease[] = $r->agentRelease;
					}else{
						$releases = $r;
					}
					foreach($releases->agentRelease as $release) {
						$r = AgentReleaseQuery::create()
							->filterByUpgradeTree($release->tree)
							->filterByRelease($release->release)
							->filterBySubRelease($release->subRelease)
							->filterByVersion($release->version)
							->findOneOrCreate();
						if($r->isNew()){
							/* @var $r agentRelease */
							$r->setAgent(base64_decode($release->agent));
							$r->setReleaseDate($release->releaseDate);
							$r->setAgentSize($release->agentSize);
							$r->save();
							$replicatedAgents++;
						}
					}
				}
				return '[agentReplication] '.$replicatedAgents.' agents replicated';
			}
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e);
		}catch(Exception $e){
			throw $e;
		}
	}
	
	public function getVersionMappingAgent($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			$dbCon = Propel::getConnection(VersionMappingAgentPeer::DATABASE_NAME);
			$dbCon->beginTransaction();
			if(is_array($conns)){
				foreach($conns as $conn){
					$v = $conn->getVersionMappingAgent(YaffmapConfig::get('version'))->ArrayOfVersionMappingAgent;
					if(!is_array($v)){
						// TODO remove workaround
						$versions[] = $v;
					}else{
						$versions = $v;
					}
					echo Kobold::dump($versions);
					foreach($versions as $version){
//						$v = VersionMappingAgentQuery::create()
//							->filterByBackendRelease($version->backendRelease)
//							->filterByAgentRelease($version->agentRelease)
//							->filterByAgentSubRelease($version->agentSubRelease)
//							->filterByAgentUpgradeTree($version->agentTree)
//							->filterByAgentVersion($version->agentVersion)
//							->findOneOrCreate($dbCon);
//						if($v->isNew()){
//							$v->save($dbCon);
//						}
						$sql = 'INSERT IGNORE INTO '.VersionMappingAgentPeer::TABLE_NAME.' 
							('.VersionMappingAgentPeer::BACKENDRELEASE.', 
							'.VersionMappingAgentPeer::AGENTRELEASE.',
							'.VersionMappingAgentPeer::AGENTSUBRELEASE.',
							'.VersionMappingAgentPeer::AGENTTREE.',
							'.VersionMappingAgentPeer::AGENTVERSION.') VALUES (
							:BACKENDRELEASE, :AGENTRELEASE, :AGENTSUBRELEASE, :AGENTTREE, :AGENTVERSION)';
						$stmt = $dbCon->prepare($sql);
						$stmt->execute(array(':BACKENDRELEASE' => $version->backendRelease, 
							':AGENTRELEASE' => $version->agentRelease,
							':AGENTSUBRELEASE' => $version->agentSubRelease,
							':AGENTTREE' => $version->agentTree,
							':AGENTVERSION' => $version->agentVersion));
					}
				}
			}
			$dbCon->commit();
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw new YaffmapException($e);
		}
	}
	
	public function getVersionMappingBackend($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			$dbCon = Propel::getConnection(VersionMappingBackendPeer::DATABASE_NAME);
			$dbCon->beginTransaction();
			if(is_array($conns)){
				foreach($conns as $conn){
					$v = $conn->getVersionMappingBackend(YaffmapConfig::get('version'))->ArrayOfVersionMappingBackend;
					if(!is_array($v)){
						// TODO remove workaround
						$versions[] = $v;
					}else{
						$versions = $v;
					}
					echo Kobold::dump($versions);
					foreach($versions as $version){
						$sql = 'INSERT IGNORE INTO '.VersionMappingBackendPeer::TABLE_NAME.' 
							('.VersionMappingBackendPeer::SERVERRELEASE.', 
							'.VersionMappingBackendPeer::CLIENTRELEASE.') VALUES (
							:SERVERRELEASE, :CLIENTRELEASE)';
						$stmt = $dbCon->prepare($sql);
						$stmt->execute(array(':SERVERRELEASE' => $version->serverRelease, ':CLIENTRELEASE' => $version->clientRelease));
					}
				}
			}
			$dbCon->commit();
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw $e;
		}
	}
	
	public function replicateNodes($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			if(is_array($conns)){
				foreach($conns as $conn){
					/* @var $conn YaffmapSoapServer */
					$n = $conn->replicateNodes(YaffmapConfig::get('version'), YaffmapConfig::get('id'));
					/* @var $n sArrayOfFfNodes */
					if(!is_array($n->ffNodes)){
						$tmp = $n->ffNodes;
						$n->ffNodes = array();
						$n->ffNodes[] = $tmp;
						unset($tmp);
					}
					$countNewNodes = 0;
					$countUpdatedReplicatedNodes = 0;
					$countSkipedNodes = 0;
					$countErrors = 0;
					foreach($n->ffNodes as $node){
						/* @var $node sFfNode */
						if(count($node->addresses) == 0){
							continue;
						}
						$dbCon = Propel::getConnection(VersionMappingBackendPeer::DATABASE_NAME);
						$dbCon->beginTransaction();
						try{
							$localNode = FfNode::findOneByAddrArray($node->addresses, $node->hostname, $dbCon);
							if(is_null($localNode)){
								// node to be replicated does not exist, create it
								$countNewNodes++;
								$localNode = FfNode::createOne($node);
								$localNode->save($dbCon);
								$wlDevices = array();
								if(is_array($node->wlDevices)){
									$wlDevices = $node->wlDevices;
								}elseif(empty($node->wlDevices)){
									// there is no wlDevice, do nothing
								}else{
									// only one wlDevice given
									$wlDevices[] = $node->wlDevices;
								}
								foreach($wlDevices as $wld){
									$wlDevice = WlDevice::createOne($wld, $localNode);
									$wlDevice->save($dbCon);
									$wlIfaces = array();
									if(is_array($wld->wlIfaces)){
										$wlIfaces = $wld->wlIfaces;
									}elseif(empty($wld->wlIfaces)){
										// there is no wlIface, do nothing
									}else{
										// only one wlIface given
										$wlIfaces[] = $wld->wlIfaces;
									}
									foreach($wlIfaces as $wli){
										/* @var $wli sWlIface */
										// check, if addrMap belongs to a bridge
										$addrMap = AddrMapQuery::create()
											->filterById($wli->addrMap->id)
											->findOne($dbCon);
										if($addrMap == null){
											$addrMap = AddrMap::createOne($wli->addrMap);
											$addrMap->save($dbCon);
										}
										$wlIface = WlIface::createOne($wli, $wlDevice, $addrMap);
										$wlIface->save($dbCon);
										// insert rf oneway links
										if($wli->rfLinksOneWay != '' && !is_array($wli->rfLinksOneWay)){
											$tmp = $wli->rfLinksOneWay;
											$wli->rfLinksOneWay = array();
											$wli->rfLinksOneWay[] = $tmp;
											unset($tmp);
										}
										if(is_array($wli->rfLinksOneWay)){
											foreach($wli->rfLinksOneWay as $rfLinkOneWay){
												/* @var $rfLinkOneWay sRfLinkOneWay */
												$existing1wLinks = RfLinkOneWayQuery::create()->findByDestMac($wlIface->getWlMacAddr());
												/* @var $existing1wLinks RfLinkOneWay */
												if($existing1wLinks->count() == 0){
													// $wlIface NICHT als destination in onewaylinks vorhanden
													$destWlIface = WlIfaceQuery::create()->findByWlMacAddr($rfLinkOneWay->destMac);
													if($destWlIface->count() == 0){
														// destination mac nicht in WlIfaces vorhanden
														$rfLink1Way = new RfLinkOneWay();
														/* @var $rfLink1Way RfLinkOneWay */
														$rfLink1Way->setDestMac($rfLinkOneWay->destMac);
														$rfLink1Way->setRssi($rfLinkOneWay->rssi);
														$rfLink1Way->setSourceWlIfaceOneWay($wlIface);
														$rfLink1Way->setTxRate($rfLinkOneWay->txRate);
														$rfLink1Way->save();
													}elseif($destWlIface->count() == 1){
														// destination mac in WlIfaces vorhanden
														$rfLink = new RfLink();
														$rfLink->setDestWlIface($destWlIface->getFirst());
														$rfLink->setSourceWlIface($wlIface);
														$rfLink->setRssi($rfLinkOneWay->rssi);
														$rfLink->setTxRate($rfLinkOneWay->txRate);
														$rfLink->save();
													}// else $destWlIface->count() > 1 => mehrere gleine wlMacAddr gefunden, überspringen
												}else{
													// $wlIface als destination in onewaylinks vorhanden, 
													// erstelle neuen rfLink und lösche rfLinkOneWay
													$rfLink = new RfLink();
													$rfLink->setDestWlIface($wlIface);
													$rfLink->setSourceWlIface($existing1wLinks->getSourceWlIfaceOneWay());
													$rfLink->setRssi($existing1wLinks->getRssi());
													$rfLink->setTxRate($existing1wLinks->getTxRate());
													$rfLink->save();
													$existing1wLinks->delete();
												}
											}
										}
										$ipAliase = array();
										if(is_array($wli->addrMap->ipAlias)){
											$ipAliase = $wli->addrMap->ipAlias;
										}elseif(empty($wli->addrMap->ipAlias)){
											// there is no ipAlias, do nothing
										}else{
											// only one ipAlias given
											$ipAliase[] = $wli->addrMap->ipAlias;
										}
										foreach($ipAliase as $ipAlias){
											$ipa = IpAlias::createOne($ipAlias, $addrMap);
											$ipa->save($dbCon);
										}
									}
								}
								$wiredIfaces = array();
								if(is_array($node->wiredIfaces)){
									$wiredIfaces = $node->wiredIfaces;
								}elseif(empty($node->wiredIfaces)){
									// there is no wiredIface, do nothing
								}else{
									// only one wiredIface given
									$wiredIfaces[] = $node->wiredIfaces;
								}
								foreach($wiredIfaces as $wiredIface){
									// check, if addrMap belongs to a bridge
									$addrMap = AddrMapQuery::create()
										->filterById($wiredIface->addrMap->id)
										->findOne($dbCon);
									if($addrMap == null){
										$addrMap = AddrMap::createOne($wiredIface->addrMap);
										$addrMap->save($dbCon);
									}
									$wiIf = WiredIface::createOne($wiredIface, $localNode, $addrMap);
									$wiIf->save($dbCon);
									$ipAliase = array();
									if(is_array($wiredIface->addrMap->ipAlias)){
										$ipAliase = $wiredIface->addrMap->ipAlias;
									}elseif(empty($wiredIface->addrMap->ipAlias)){
										// there is no ipAliase, do nothing
									}else{
										// only one ipAliase given
										$ipAliase[] = $wiredIface->addrMap->ipAlias;
									}
									foreach($ipAliase as $ipAlias){
										$ipa = IpAlias::createOne($ipAlias, $addrMap);
										$ipa->save($dbCon);
									}
								}
							}elseif($localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'true' 
								&& is_null($localNode->getReplicatedBy())){
								// local and to be replicated node are global updated, skip!
								$countSkipedNodes++;
								continue;
							}elseif($localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'false'){
								// overwrite global updated node
								echo 'overwrite global updated node with not global updated node';
// 								foreach(get_object_vars($update) as $key => $val){
// 									if(!(is_array($val) || $key == 'id' || is_object($val))){
// 										if($key == 'isHna'){
// 											if($val == 'true'){
// 												$this->setIsHna(true);
// 											}else{
// 												$this->setIsHna(false);
// 											}
// 											continue;
// 										}
// 										try{
// 											call_user_func_array(array($this, 'set'.ucfirst($key)), array($val));
// 										}catch(Exception $e){
// 											throw new EUnknownAttribute($key);
// 										}
// 									}
// 								}
							}elseif(!$localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'true'){
								// if lastest update < now - update interval then update it else skip
								echo 'update node with global update';
							}elseif(!is_null($localNode->getReplicatedBy())){
								// update previously replicated nodes
								$countUpdatedReplicatedNodes++;
								foreach(get_object_vars($node) as $key => $val){
									if(!(is_array($val) || $key == 'id' || is_object($val) 
										|| $key == 'addresses' || $key == 'wiredIfaces' 
										|| $key == '$wlDevices')){
										if($key == 'isHna'){
											if($val == 'true'){
												$localNode->setIsHna(true);
											}else{
												$localNode->setIsHna(false);
											}
											continue;
										}
										try{
											call_user_func_array(array($localNode, 'set'.ucfirst($key)), array($val));
										}catch(Exception $e){
											throw new EUnknownAttribute($key);
										}
									}
								}
								$localNode->setUpdatedAt(new DateTime("now"));
								$localNode->save($dbCon);
								if(is_array($node->wiredIfaces)){
									foreach($node->wiredIfaces as $wiredIface){
										/* @var $wiredIface sWiredIface */
										// update wired interface
										$wifQuery = WiredIfaceQuery::create()
											->filterByFfNode($localNode)
											->filterByName($wiredIface->name);
										if($wiredIface->bridgeName != ''){
											$wifQuery->filterByBridgeName($wiredIface->bridgeName);
										}
										$wif = $wifQuery->findoneOrCreate($dbCon);
										/* @var $wif WiredIface */
										$wif->setUpdatedAt(new DateTime("now"));
										// update address map
										$addrMapQuery = AddrMapQuery::create()->filterByWiredIface($wif);
										if($wiredIface->addrMap->ipv4Addr != ''){
											$addrMapQuery->filterByIpv4addr($wiredIface->addrMap->ipv4Addr);
										}
										if($wiredIface->addrMap->ipv6Addr != ''){
											$addrMapQuery->filterByIpv6addr($wiredIface->addrMap->ipv6Addr);
										}
										if($wiredIface->addrMap->macAddr != ''){
											$addrMapQuery->filterByMacAddr($wiredIface->addrMap->macAddr);
										}
										$addrMap = $addrMapQuery->findOneOrCreate($dbCon);
										$addrMap->setUpdatedAt(new DateTime("now"));
										$addrMap->setBridgeName($wiredIface->addrMap->bridgeName);
										if($wiredIface->addrMap->isGlobalUpdated == 'true'){
											$addrMap->setIsGlobalUpdated(true);
										}else{
											$addrMap->setIsGlobalUpdated(false);
										}
										$addrMap->save($dbCon);
										$wif->setAddrMap($addrMap);
										$wif->save($dbCon);
										if(!is_array($wlIface->addrMap->ipAlias) && $wlIface->addrMap->ipAlias != ''){
											$tmp = $wlIface->addrMap->ipAlias;
											$wlIface->addrMap->ipAlias = array();
											$wlIface->addrMap->ipAlias[] = $tmp;
											unset($tmp);
										}
										if(is_array($wiredIface->addrMap->ipAlias)){
											// update ip alias
											foreach($wiredIface->addrMap->ipAlias as $ipAlias){
												/* @var $ipAlias sIpAlias */
												$ipAliasQuery = IpAliasQuery::create()->filterByAddrMap($addrMap);
												if($ipAlias->ipv4Addr != ''){
													$ipAliasQuery->filterByIpv4addr($ipAlias->ipv4Addr);
												}
												if($ipAlias->ipv6Addr != ''){
													$ipAliasQuery->filterByIpv6addr($ipAlias->ipv6Addr);
												}
												$ipA = $ipAliasQuery->findOneOrCreate($dbCon);
												$ipA->setName($ipAlias->name);
												/* @var $ipA IpAlias */
												$ipA->setUpdatedAt(new DateTime("now"));
												$ipA->save($dbCon);
											}
										}
									}
								}
								if(!is_array($node->wlDevices) && $node->wlDevices != ''){
									$tmp = $node->wlDevices;
									$node->wlDevices = array();
									$node->wlDevices[] = $tmp;
									unset($tmp);
								}
								if(is_array($node->wlDevices)){
									foreach($node->wlDevices as $wlDevice){
										/* @var $wlDevice sWlDevice */
										// update wireless devices
										$wlD = WlDeviceQuery::create()
											->filterByFfNode($localNode)
											->filterByName($wlDevice->name)
											->findOneOrCreate($dbCon);
										/* @var $wlD WlDevice */
										$wlD->setTxpower($wlDevice->txpower);
										$wlD->setAntDirection($wlDevice->antDirection);
										$wlD->setAntbeamh($wlDevice->antBeamH);
										$wlD->setAntbeamv($wlDevice->antBeamV);
										$wlD->setAntgain($wlDevice->antGain);
										$wlD->setAnttilt($wlDevice->antTilt);
										$wlD->setAntpol($wlDevice->antPol);
										$wlD->setChannel($wlDevice->channel);
										$wlD->setWirelessStandard($wlDevice->wirelessStandard);
										$wlD->setFrequency($wlDevice->frequency);
										$wlD->setAvailfrequency($wlDevice->availFrequency);
										$wlD->setUpdatedAt(new DateTime("now"));
										$wlD->save($dbCon);
										// update wireless Interface
										if(!is_array($wlDevice->wlIfaces)){
											$tmp = $wlDevice->wlIfaces;
											$wlDevice->wlIfaces = array();
											$wlDevice->wlIfaces[] = $tmp;
											unset($tmp);
										}
										foreach($wlDevice->wlIfaces as $wlIface){
											/* @var $wlIface sWlIface */
// 											if($wlIface->wlMacAddr == ''){
// 												//TODO wlMacAddr missing bugfix, refs #171
// 												continue;
// 											}
											$wlI = WlIfaceQuery::create()->filterByWlDevice($wlD)
												->filterByWlMacAddr($wlIface->wlMacAddr)
												->filterByName($wlIface->name)
												->findOneOrCreate($dbCon);
											/* @var $wlI WlIface */
											$wlI->setWlMode($wlIface->wlMode);
											$wlI->setBssid($wlIface->bssid);
											$wlI->setEssid($wlIface->essid);
											$wlI->setBridgeName($wlIface->bridgeName);
											$wlI->setUpdatedAt(new DateTime("now"));
											// update address map
											$addrMapQuery = null;
											$addrMapQuery = AddrMapQuery::create();
											if($wlIface->addrMap->ipv4Addr != ''){
												$addrMapQuery->filterByIpv4addr($wlIface->addrMap->ipv4Addr);
											}
											if($wlIface->addrMap->ipv6Addr != ''){
												$addrMapQuery->filterByIpv6addr($wlIface->addrMap->ipv6Addr);
											}
											if($wlIface->addrMap->macAddr != ''){
												$addrMapQuery->filterByMacAddr($wlIface->addrMap->macAddr);
											}
											$addrMap = $addrMapQuery->findOneOrCreate($dbCon);
											$addrMap->setUpdatedAt(new DateTime("now"));
											$addrMap->setBridgeName($wlIface->addrMap->bridgeName);
											if($wlIface->addrMap->isGlobalUpdated == 'true'){
												$addrMap->setIsGlobalUpdated(true);
											}else{
												$addrMap->setIsGlobalUpdated(false);
											}
											if($addrMap->isNew()){
												/**
												 * zu bestehendem device ist ein addreemap hinzugekommen, 
												 * also muss auch das wireless interface neu sein.
												 * bei global upgedateten knoten lassen sich die wireless 
												 * interfaces eines devices nicht unterscheiden, somit kann
												 * das oben gesuchte wireless interface das falsche sein.
												 * es muss ein neues wireless interface erstellt werden.
												 */
												$wlI = new WlIface();
												$wlI->setWlDevice($wlD);
												$wlI->setWlMacAddr($wlIface->wlMacAddr);
												$wlI->setName($wlIface->name);
												$wlI->setWlMode($wlIface->wlMode);
												$wlI->setBssid($wlIface->bssid);
												$wlI->setEssid($wlIface->essid);
												$wlI->setBridgeName($wlIface->bridgeName);
												$wlI->setUpdatedAt(new DateTime("now"));
												$wlI->setAddrMap($addrMap);
											}
											$addrMap->save($dbCon);
											if($wlI->isNew()){
												$wlI->setAddrMap($addrMap);
											}
											$wlI->save($dbCon);
											// TODO untested, because of missing wlMacAddr bug #171
											if($wlIface->rfLinksOneWay != '' && !is_array($wlIface->rfLinksOneWay)){
												$tmp = $wlIface->rfLinksOneWay;
												$wlIface->rfLinksOneWay = array();
												$wlIface->rfLinksOneWay[] = $tmp;
												unset($tmp);
											}
											if(is_array($wlIface->rfLinksOneWay)){
												foreach($wlIface->rfLinksOneWay as $rfLinkOneWay){
													/* @var $rfLinkOneWay sRfLinkOneWay */
													// check if oneWayLink can be converted to rfLink
													$existing1wLinks = RfLinkOneWayQuery::create()->findByDestMac($wlI->getWlMacAddr());
													/* @var $existing1wLinks RfLinkOneWay */
													if($existing1wLinks->count() == 0){
														// update rfOneWayLink
														$rfOneWayLink = RfLinkOneWayQuery::create()->filterBySourceWlIfaceOneWay($wlI)->filterByDestMac($rfLinkOneWay->destMac)->findOneOrCreate();
														/* @var $rfOneWayLink RfLinkOneWay */
														$rfOneWayLink->setRssi($rfLinkOneWay->rssi);
														$rfOneWayLink->setTxRate($rfLinkOneWay->txRate);
// 														$rfOneWayLink->save();
													}elseif($existing1wLinks->count() == 1){
														// $wlIface als destination in onewaylinks vorhanden,
														// erstelle neuen rfLink und lösche rfLinkOneWay
														$rfLink = new RfLink();
														$rfLink->setDestWlIface($wlI);
														$rfLink->setSourceWlIface($existing1wLinks->getSourceWlIfaceOneWay());
														$rfLink->setRssi($existing1wLinks->getRssi());
														$rfLink->setTxRate($existing1wLinks->getTxRate());
														$rfLink->save();
														$existing1wLinks->delete();
													}
												}
											}
											if(!is_array($wlIface->addrMap->ipAlias) && $wlIface->addrMap->ipAlias != ''){
												$tmp = $wlIface->addrMap->ipAlias;
												$wlIface->addrMap->ipAlias = array();
												$wlIface->addrMap->ipAlias[] = $tmp;
												unset($tmp);
											}
											if(is_array($wlIface->addrMap->ipAlias)){
												// update ip alias
												foreach($wlIface->addrMap->ipAlias as $ipAlias){
													/* @var $ipAlias sIpAlias */
													$ipAliasQuery = IpAliasQuery::create()->filterByAddrMap($addrMap);
													if($ipAlias->ipv4Addr != ''){
														$ipAliasQuery->filterByIpv4addr($ipAlias->ipv4Addr);
													}
													if($ipAlias->ipv6Addr != ''){
														$ipAliasQuery->filterByIpv6addr($ipAlias->ipv6Addr);
													}
													$ipA = $ipAliasQuery->findOneOrCreate($dbCon);
													$ipA->setName($ipAlias->name);
													/* @var $ipA IpAlias */
													$ipA->setUpdatedAt(new DateTime("now"));
													$ipA->save($dbCon);
												}
											}
										}
									}
								}
							}else{
								
							}
							$dbCon->commit();
						}catch (Exception $e) {
							echo Kobold::dump($node);
							$dbCon->rollback();
							$error = new ErrorLog();
							$error->setMessage($e);
							$error->setType('replication');
							$error->save();
							$countErrors++;
						}
					}
					echo 'new nodes: '.$countNewNodes.'<br>';
					echo 'countUpdatedReplicatedNodes: '.$countUpdatedReplicatedNodes.'<br>';
					echo 'countSkipedNodes: '.$countSkipedNodes.'<br>';
					echo 'countErrors: '.$countErrors.'<br>';
				}
			}
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw $e;
		}
	}
}
?>