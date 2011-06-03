<?php
class YaffmapNodeUpdate extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
//		$allowed = array();
//		$this->checkInput($allowed);
	}
	
	public function nodeUpdate($version, $tree, $release){
		if(!isset($this->request->id) || $this->request->id == ''){
			throw new EIsufficientQuery('NodeID missing.');
		}
		$node = null;
		$node = FfNode::getNodeById($this->request->id);
		if($node == null){
			// node was not found in database, tell agent to run getID
			$this->response->setErrorCode(YaffmapResponse::NODEID_NOT_FOUND);
			$this->response->setErrorMsg('NodeID not found, please run getID.');
			return $this->response;
		}
		$node->setUpdatedAt(new DateTime("now"));
		// update node
		$node->updateNode($this->request, $version, $tree, $release);
		$node->save();
		$this->response->addData('id="'.$node->getId().'"');
		// update wired interfaces
		if(is_array($this->request->wiredIface)){
			$node->updateWiredInterface($this->request->wiredIface);
		}
		//update wireless interfaces
		if(is_array($this->request->wlDevice)){
			$node->updateWlInterface($this->request->wlDevice);
		}
		// update rf links
		if(is_array($this->request->rfNeighbour)){
			$node->updateRfLink($this->request->rfNeighbour);
		}
		// update rp links
		if(is_object($this->request->neighbour)){
			$node->updateRpLink($this->request->neighbour);
		}
		$this->response->setErrorCode(YaffmapResponse::OPERATION_SUCCEDED);
		$this->response->setErrorMsg('Operation Succeded.');
		return $this->response;
	}
}
?>