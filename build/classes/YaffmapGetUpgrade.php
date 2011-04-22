<?php

class YaffmapGetUpgrade extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('version', 'tree', 'rel', 'updateToRel');
		$this->checkInput($allowed);
	}
	
	/**
	 * 
	 * @throws PropelException
	 * @throws YaffmapException
	 */
	public function getUpgrade(){
		if(isset($this->request['version']) && isset($this->request['tree'])){
			// version and tree given
			if(isset($this->request['rel']) && !isset($this->request['updateToRel'])){
				$tmp = explode("-", $this->request['rel']);
				$relName = $tmp[0];
				$relSubName = $tmp[1];
				// installed release given, do not force update to specified release
				$installedVersion = AgentReleaseQuery::create()
					->filterByVersion($this->request['version'])
					->filterByUpgradeTree($this->request['tree'])
					->filterByRelease($relName)
					->filterBySubRelease($relSubName)
					->findOneOrCreate();
				if(!$installedVersion->getIsHead()){
					// installed release is not up to date, install head of given tree
					$updateTo = AgentReleaseQuery::create()
						->filterByUpgradeTree($this->request['tree'])
						->filterByIsHead(true)
						->filterByVersion($this->request['version'])
						->findOne();
					if($relName >= $updateTo->getRelease() && $relSubName > $updateTo->getSubRelease()){
						// installed release is newer then release to be installed
//						throw new YaffmapLoggedException('i\'ll not downgrade! i\'ll not downgrade!');
					}else{
						if($updateTo != null){
							$this->response->setErrorCode(ResponseCodeNode::OPERATION_SUCCEDED);
							$this->response->setErrorMsg('Update to release '.$updateTo->getRelease().'-'.$updateTo->getSubRelease().' found.');
							$this->response->addData('url="'.$updateTo->getUrl().'"');
						}else{
							// version or tree not found
							$this->response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
							$this->response->setErrorMsg('No update found.');
						}
					}
				}else{
					// no update found
					$this->response->setErrorCode(ResponseCodeNode::OPERATION_SUCCEDED);
					$this->response->setErrorMsg('Latest release('.$installedVersion->getRelease().'-'.$installedVersion->getSubRelease().') already installed.');
				}
			}elseif(!isset($this->request['rel']) && !isset($this->request['updateToRel'])){
				// installed release not given, install head of given tree
				$updateTo = AgentReleaseQuery::create()
					->filterByUpgradeTree($this->request['tree'])
					->filterByIsHead(true)
					->filterByVersion($this->request['version'])
					->findOne();
				if($updateTo != null){
					$this->response->setErrorCode(ResponseCodeNode::OPERATION_SUCCEDED);
					$this->response->setErrorMsg('Update to release '.$updateTo->getRelease().'-'.$updateTo->getSubRelease().' found.');
					$this->response->addData('url="'.$updateTo->getUrl().'"');
				}else{
					// version or tree not found
					$this->response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
					$this->response->setErrorMsg('No update found.');
				}
			}elseif(isset($this->request['updateToRel'])){
				$tmp = explode("-", $this->request['updateToRel']);
				$relName = $tmp[0];
				$relSubName = $tmp[1];
				// force to update to a given release
				$updateTo = AgentReleaseQuery::create()
					->filterByVersion($this->request['version'])
					->filterByRelease($relName)
					->filterBySubRelease($relSubName)
					->filterByUpgradeTree($this->request['tree'])
					->findOneOrCreate();
				if($updateTo->isNew()){
					// revision not found, ahhh
					$this->response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
					$this->response->setErrorMsg('Requested release or tree not found. Update failed.');
				}else{
					$this->response->setErrorCode(ResponseCodeNode::OPERATION_SUCCEDED);
					$this->response->setErrorMsg('Update release to '.$updateTo->getRelease().'-'.$updateTo->getSubRelease().' of '.$updateTo->getDevelopmentTree().' tree.');
					$this->response->addData('url="'.$updateTo->getUrl().'"');	
				}
			}else{
				$this->response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
				$this->response->setErrorMsg('Update fauled.');
			}
		}else{
			// version or tree not given
			$this->response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
			$this->response->setErrorMsg('release or tree not given. Update fauled.');
		}
		return $this->response;
	}
}
?>