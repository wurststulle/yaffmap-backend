<?php
ini_set("soap.wsdl_cache_enabled", "0");

class YaffmapBackend{
	
	/**
	 * @var Config
	 */
	protected $config = null;
	
	public function __construct(){
		$this->config = Config::getConfig();
	}
	
	public function generateId(){
		return $this->config->getId();
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
					$result = $conn->announceBackend($this->config->getId(), $this->config->getUrl(), $this->config->getVersion());
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
					$b = $conn->getBackends($this->config->getVersion())->backends;
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
						}elseif($b->isNew() && $b->getId() != $this->config->getId()){
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
	 * get head agent releases from backend with given url, or all known backends
	 * TODO save to database
	 * 
	 * @param string $url
	 * @throws YaffmapException
	 * @throws YaffmapSoapException
	 */
	public function getAgentRelease($url = null){
		try{
			$client = new YaffmapSoapClient($url);
			$conns = $client->getBackendConns();
			if(is_writable(dirname(__FILE__).'/../')){
				if(is_array($conns)){
					foreach($conns as $conn){
						$release = $conn->getAgentRelease($this->config->getVersion());
						$path = dirname(__FILE__).'/../download/'.$release->agentRelease->release.'/yaffmap_'.$release->agentRelease->release.'-'.$release->agentRelease->subRelease.'_'.$release->agentRelease->version.'_'.$release->agentRelease->tree.'.tar.gz';
						if(!file_exists($path)){
							// download remote file
							mkdir(dirname(__FILE__).'/../download/'.$release->agentRelease->release, 0755, true);
						    if($fp = fopen($path, 'w')){
							    $ch = curl_init($release->agentRelease->url);
							    curl_setopt($ch, CURLOPT_FILE, $fp);
							    $data = curl_exec($ch);
							    curl_close($ch);
							    fclose($fp);
						    }else{
						    	throw new YaffmapException('failed to open file.');
						    }
						    $oldHead = UpgradeQuery::create()->filterByIsHead(true)->findOne();
						    if($oldHead != null){
						    	$oldHead->setIsHead(false);
						    	$oldHead->save();
						    }
						    $ar = new Upgrade();
						    $ar->setRelease($release->agentRelease->release);
						    $ar->setSubRelease($release->agentRelease->subRelease);
						    $ar->setUpgradeTree($release->agentRelease->tree);
						    $ar->setVersion($release->agentRelease->version);
						    $ar->setUrl($release->agentRelease->url);
						    $ar->setReleaseDate($release->agentRelease->releaseDate);
						    $ar->setIsHead(true);
						    $ar->save();				    
						}
					}
				}
			}else{
				throw new YaffmapException('base dir not writeable.');
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
					$v = $conn->getVersionMappingAgent($this->config->getVersion())->ArrayOfVersionMappingAgent;
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
					$v = $conn->getVersionMappingBackend($this->config->getVersion())->ArrayOfVersionMappingBackend;
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
}
?>