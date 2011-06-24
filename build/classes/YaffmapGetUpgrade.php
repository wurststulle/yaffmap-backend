<?php
class YaffmapGetUpgrade extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$this->checkInput(null, true, true);
	}
	
	/**
	 * 
	 * @throws PropelException
	 * @throws YaffmapException
	 */
	public function getUpgrade(){
		$tmp = explode("-", $this->request['release']);
		$relName = $tmp[0];
		$relSubName = $tmp[1];
		$latestAgent = null;
		$latestAgent = AgentRelease::getLatest($this->request['version'], $this->request['tree']);
		if($latestAgent == null){
			$latestDefaultAgent = AgentRelease::getLatest($this->request['version'], YaffmapConfig::get('defaultTree'));
			if($latestDefaultAgent == null){
				$this->response->setResponseCode(YaffmapResponse::OPERATION_FAILED);
				$this->response->setResponseMsg('Given version is not known by this backend.');
			}else{
				$this->response->setResponseCode(YaffmapResponse::NEW_AGENT_RELEASE_FOUND);
				$this->response->setResponseMsg('Given tree not known by this backend, please install default tree('.$latestDefaultAgent->getUrl().').');
				$this->response->addResponseData($latestDefaultAgent->getUrl());
			}
		}else{
			if($latestAgent->getRelease() > $relName || $latestAgent->getSubRelease() > $relSubName){
				// there is a newer agent, update!
				$this->response->setResponseCode(YaffmapResponse::NEW_AGENT_RELEASE_FOUND);
				$this->response->setResponseMsg('New agent release found.');
				$this->response->addResponseData($latestAgent->getUrl());
			}else{
				$this->response->setResponseCode(YaffmapResponse::NEW_AGENT_RELEASE_NOT_FOUND);
				$this->response->setResponseMsg('No new agent release found.');
			}
		}
		return $this->response;
	}
}
?>