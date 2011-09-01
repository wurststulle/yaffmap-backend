<?php
class YaffmapNewAgentRelease extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('uploadedFile', 'mapping');
		$this->checkInput($allowed, false, true);
	}
	
	public function newAgentReleaseWithFile(){
		$hasError = true;
		$tmp = explode("-", $this->request['release']);
		$relName = $tmp[0];
		$relSubName = $tmp[1];
		if(!isset($this->request['tree']) || !isset($this->request['release']) || !isset($this->request['version'])){
			throw new EIsufficientQuery('tree, release or version missing.');
		}
		$release = AgentReleaseQuery::create()
			->filterByUpgradeTree($this->request['tree'])
			->filterByRelease($relName)
			->filterBySubRelease($relSubName)
			->filterByVersion($this->request['version'])
			->findOneOrCreate();
		if(!$release->isNew()){
			throw new YaffmapLoggedException('Release already exists.');
		}else{
			/* @var $release agentRelease */
			if(!is_writeable('download')){
				throw new Exception('Directory "download" is not writeable.');
			}
			$putData = file_get_contents("php://input");
			file_put_contents('download/tmp.tar.gz', $putData);
			$size = filesize('download/tmp.tar.gz');
			$release->setAgent($putData);
			$release->setAgentSize($size);
			unlink('download/tmp.tar.gz');
			$release->setReleaseDate(new DateTime("now"));
			$release->save();
            unset($putData);
            if($this->request['mapping'] == '1'){
	            $mapping = new VersionMappingAgent();
	            $mapping->setAgentRelease($relName);
	            $mapping->setAgentSubRelease($relSubName);
	            $mapping->setAgentUpgradeTree($this->request['tree']);
	            $mapping->setAgentVersion($this->request['version']);
	            $mapping->setBackendRelease(YaffmapConfig::get('version'));
	            $mapping->save();
            }
		}
		$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
		return $this->response;
	}
}
?>