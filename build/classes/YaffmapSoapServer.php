<?php
ini_set("soap.wsdl_cache_enabled", "0");

class YaffmapSoapServer{
	
	/**
	 * @var Config
	 * @ignore wsdl
	 */
	protected $config = null;
	
	/**
	 * @var object SoapServer
	 * @ignore wsdl
	 */
	protected $soapServer = null;
	
	/**
	 * @var string
	 * @ignore wsdl
	 */
	protected $url = null;
	
	/**
	 * @var string
	 * @ignore wsdl
	 */
	protected $path = ''; // path from yaffmap root to soap.php
	
	/**
	 * @ignore wsdl
	 */
	public function __construct(){
		$this->config = Config::getConfig();
		$this->url = $this->config->getUrl().$this->path;
	}
	
	/**
	 * @ignore wsdl
	 */
	public function createSoapServer(){
		$this->soapServer = new SoapServer($this->url.'/soap.php?wsdl', array('classmap' => SoapClassMap::getMap()));
		$this->soapServer->setClass(get_class($this));
	}
	
	/**
	 * @ignore wsdl
	 */
	public function handle(){
		if($this->soapServer == null){
			$this->createSoapServer();
		}
		$this->soapServer->handle();
	}
	
	/**
	 * check, if version of this backend is compatible with client backends version
	 * 
	 * @ignore wsdl
	 * @param string $version
	 */
	private function checkVersion($version){
		if(VersionMappingBackendQuery::create()->filterByServerRelease($this->config->getVersion())->filterByClientRelease($version)->count() == 0){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	 * @param string $version
	 * @return ArrayOfAgentRelease
	 */
	public function getAgentRelease($version){
		if(!$this->checkVersion($version)){
			return new SoapFault(null, 'Your backend is outdated, please update it.');
		}
		$releases = UpgradeQuery::create()->filterByIsHead(true)->find();
		if($releases == null){
			return new SoapFault('no agent release found.');
		}else{
			$list = new sArrayOfAgentRelease();
			foreach($releases as $release){
				$list->agentRelease[] = $release->getSoapClass();
			}
			return $list;
		}
	}
	
	/**
	 * @param string $version
	 * @return ArrayOfVersionMappingAgent
	 */
	public function getVersionMappingAgent($version){
		if(!$this->checkVersion($version)){
			return new SoapFault(null, 'Your backend is outdated, please update it.');
		}
		$maps = VersionMappingAgentQuery::create()->find();
		$arr = new sArrayOfVersionMappingAgent();
		foreach($maps as $map){
			$arr->ArrayOfVersionMappingAgent[] = $map->getSoapClass();
		}
		return $arr;
	}
	
	/**
	 * @param string $version
	 * @return ArrayOfVersionMappingBackend
	 */
	public function getVersionMappingBackend($version){
		if(!$this->checkVersion($version)){
			return new SoapFault(null, 'Your backend is outdated, please update it.');
		}
		$maps = VersionMappingBackendQuery::create()->find();
		$arr = new sArrayOfVersionMappingBackend();
		foreach($maps as $map){
			$arr->ArrayOfVersionMappingBackend[] = $map->getSoapClass();
		}
		return $arr;
	}
	
	/**
	 * add announced backend to database
	 * 
	 * @param string $id
	 * @param string $url
	 * @param string $version
	 * @return string
	 */
	public function announceBackend($id, $url, $version){
		if(!$this->checkVersion($version)){
			return new SoapFault(null, 'Your backend is outdated, please update it.');
		}
		$backend = BackendsQuery::create()->filterById($id)->findOneOrCreate();
		if($backend->isNew()){
			// publish new backend
			$backend->setUrl($url);
			$backend->save();
			return '['.$this->config->getUrl().']: backend ('.$url.') added.';
		}else{
			$backend->setUpdatedAt(new DateTime());
			$backend->save();
			return '['.$this->config->getUrl().']: backend ('.$url.') updated.';
		}
	}
	
	/**
	 * @param string $version
	 * @throws YaffmapException
	 * @return ArrayOfBackends
	 */
	public function getBackends($version){
		if(!$this->checkVersion($version)){
			return new SoapFault(null, 'Your backend is outdated, please update it.');
		}
		$list = new sArrayOfBackends();
		$backends = BackendsQuery::create()->find();
		if($backends != null){
			foreach($backends as $backend){
				$b = new sBackend();
				$b->id = $backend->getId();
				$b->url = $backend->getUrl();
				$b->updatedAt = $backend->getUpdatedAt();
				$list->backends[] = $b;
			}
		}
		$b = new sBackend();
		$b->id = $this->config->getId();
		$b->url = $this->config->getUrl();
		$dt = new DateTime();
		$b->updatedAt = $dt->format('Y-m-d H:i:s');
		$list->backends[] = $b;
		return $list;
	}
	
	/**
	 * @param string $ul
	 * @param string $lr
	 * @return ArrayOfFfNodes
	 */
	public function getFfNodes($ul, $lr){
		try{
			$nodes = FfNodeQuery::create()->find();
			$arrayOfNodes = new sArrayOfFfNodes();
			if($nodes != null){
				foreach($nodes as $node){
					$arrayOfNodes->ffNodes[] = $node->getSoapClass();
				}
			}
			return $arrayOfNodes;
		}catch(Exception $e){
			return new Exception($e);
		}
	}
	
	/**
	 * @param string $id
	 * @return FfNode
	 */
	public function getFfNode($id){
		try{
			$node = FfNodeQuery::create()->filterById($id)->findOne();
			if($node != null){
				return $node->getSoapClass();
			}else{
				return null;
			}
		}catch(Exception $e){
			return new Exception($e);
		}
	}
	
	/**
	 * @param string $ul
	 * @param string $l
	 * @return ArrayOfRpLinks
	 */
	public function getRpLinks($ul, $l){
		try{
			$rpLinks = RpLinkLocationQuery::create()->find();
			$arrayOfRpLinks = new sArrayOfRpLinks();
			if($rpLinks != null){
				foreach($rpLinks as $rpLink){
					$arrayOfRpLinks->arrayOfRpLinks[] = $rpLink->getSoapClass();
				}
			}
			return $arrayOfRpLinks;
		}catch(Exception $e){
			return new Exception($e);
		}
	}
	
	/**
	 * @param string $hostName
	 * @param string $data
	 * @return string
	 */
	public function setMisc($hostName, $data){
		try{
			$node = FfNodeQuery::create()->filterByHostname($hostName)->findOne();
			if($node != null){
				$dataJson = Yaffmap::decodeJson($data);
				$miscJson = Yaffmap::decodeJson($node->getMisc());
				$keyFound = false;
				if(is_array($miscJson)){
					foreach($miscJson as $item){
						if($item->key == $dataJson->key){
							$item->value = $dataJson->value;
							$node->setMisc(json_encode($miscJson));
							$node->save();
							$keyFound = true;
							break;
						}
					}
					if(!$keyFound){
						$miscJson[] = $dataJson;
						$node->setMisc(json_encode($miscJson));
						$node->save();
					}
				}else{
					$node->setMisc('['.$data.']');
					$node->save();
				}
				return true;
			}else{
				return false;
			}
		}catch(Exception $e){
			return new Exception($e);
		}
	}
}
?>