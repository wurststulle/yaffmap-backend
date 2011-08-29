<?php
ini_set("soap.wsdl_cache_enabled", "0");
require_once 'soapClasses.php';

class YaffmapSoapClient{
	
	protected $classmap = null;
	
	/**
	 * @var array SoapClient
	 */
	protected $backendConns = null;
	
	/**
	 * @var string
	 */
	protected $path = ''; // path from yaffmap root to soap.php
	
	/**
	 * @var string
	 */
	protected $url = null;
	
	public function __construct($url = null){
		$this->url = $url;
		$this->classmap = SoapClassMap::getMap();
		$this->checkEnv();
	}
	
	protected function checkEnv(){
		if(YaffmapConfig::get('url') == ''){
			// check existing of backend identification id
			throw new YaffmapReplicationException('backend env check failed.');
		}
	}
	
	/**
	 * @param string $url
	 * @return SoapClient
	 * @throws YaffmapSoapException
	 */
	public function connectTo($url){
		if($this->http_file_exists($url.$this->path.'/soap.php?wsdl')){
			return new SoapClient($url.$this->path.'/soap.php?wsdl', array('location' => $url.$this->path.'/soap.php', 'classmap' => $this->classmap));
		}else{
			throw new YaffmapSoapException('Can not connect to service '.$url.$this->path.'/soap.php?wsdl');
		}
	}
	private function http_file_exists($url){
		$f = @fopen($url, "r");
		if($f){
			fclose($f);
			return true;
		}
		return false;
	} 
	
	/**
	 * @return array SoapClient
	 * @throws PropelException
	 */
	public function getBackendConns(){
		if($this->url == null){
			$backends = BackendsQuery::create()->find();
			if($backends->count() != 0){
				foreach($backends as $backend){
					if($this->http_file_exists($backend->getUrl().$this->path.'/soap.php?wsdl')){
						$this->backendConns[] = new SoapClient($backend->getUrl().$this->path.'/soap.php?wsdl', array('location' => $backend->getUrl().$this->path.'/soap.php', 'classmap' => $this->classmap));
					}else{
						// TODO
						echo $backend->getUrl().$this->path.'/soap.php?wsdl'.' nicht erreichbar';
					}
				}
			}else{
				throw new YaffmapException('No backends found.');
			}
		}else{
			if($this->http_file_exists($this->url.$this->path.'/soap.php?wsdl')){
				$this->backendConns[] = new SoapClient($this->url.$this->path.'/soap.php?wsdl', array('location' => $this->url.$this->path.'/soap.php', 'classmap' => $this->classmap));
			}else{
				// TODO
				echo $this->url.$this->path.'/soap.php?wsdl'.' nicht erreichbar';
			}
		}
		return $this->backendConns;
	}
}
?>