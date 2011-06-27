<?php
class YaffmapNewAgentRelease extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('tree', 'release', 'version', 'uploadedFile');
		$this->checkInput($allowed);
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
			$putData = file_get_contents("php://input");
			$release->setAgent($putData);
			// TODO get file size
//			$release->setAgentSize(filesize($putData));
			$release->setReleaseDate(new DateTime("now"));
			$release->save();
            unset($putData);
		}
		$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
	}
}
?>