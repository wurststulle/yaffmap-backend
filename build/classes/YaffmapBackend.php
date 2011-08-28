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
			$dbCon = Propel::getConnection(FfNodePeer::DATABASE_NAME);
			$dbCon->beginTransaction();
			if(is_array($conns)){
				foreach($conns as $conn){
					/* @var $conn YaffmapSoapServer */
					$n = $conn->replicateNodes(YaffmapConfig::get('version'), YaffmapConfig::get('id'))->ffNodes;
					if(is_array($n)){
						foreach($n as $node){
							/* @var $node sFfNode */
							$localNode = FfNode::findOneByAddrArray($node->addresses, $node->hostname, $dbCon);
							if($localNode == null){
								// node to be replicated does not exist, create it
//								echo Kobold::dump($node);
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
										// check, if addrMap belongs to a bridge
										$addrMap = AddrMapQuery::create()->filterById($wli->addrMap->id)->findOne($dbCon);
										if($addrMap == null){
											$addrMap = AddrMap::createOne($wli->addrMap);
											$addrMap->save($dbCon);
										}
										$wlIface = WlIface::createOne($wli, $wlDevice, $addrMap);
										$wlIface->save($dbCon);
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
									$addrMap = AddrMapQuery::create()->filterById($wiredIface->addrMap->id)->findOne($dbCon);
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
							}elseif($localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'true'){
								// local and to be replicated node are global updated, skip!
								continue;
							}elseif($localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'false'){
								// overwrite global updated node
								// update node
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
							}elseif(!$localNode->getIsGlobalUpdated() && $node->isGlobalUpdated == 'true'){
								// if lastest update < now - update interval then update it else skip
							}
						}
					}else{
						
					}
//					echo Kobold::dump($n);
				}
			}
			$dbCon->commit();
		}catch(SoapFault $e){
			throw new YaffmapSoapException($e->getMessage());
		}catch(Exception $e){
			throw $e;
		}
	}
}
?>