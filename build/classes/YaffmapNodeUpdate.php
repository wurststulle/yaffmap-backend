<?php
class YaffmapNodeUpdate extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('node');
		$this->checkInput($allowed);
	}
	
	public function nodeUpdate(){
		$dataNode = Yaffmap::decodeJson($this->request['node']);
		if(!isset($dataNode->id) || $dataNode->id == ''){
			throw new EIsufficientQuery('NodeID missing.');
		}
		$node = null;
		$node = FfNode::getNodeById($dataNode->id);
		if($node == null){
			// node was not found in database, tell agent to run getID
			$this->response->setResponseCode(YaffmapResponse::NODEID_NOT_FOUND);
			$this->response->setResponseMsg('NodeID not found, please run getID.');
			return $this->response;
		}
		$node->setUpdatedAt(new DateTime("now"));
		// update node
		$node->updateNode($dataNode, $this->request['version'], $this->request['tree'], $this->request['release']);
		$node->save();
		$this->response->addResponseData('id="'.$node->getId().'"');
		// update wired interfaces
		if(is_array($dataNode->wiredIface)){
			$node->updateWiredInterface($dataNode->wiredIface);
		}
		//update wireless interfaces
		if(is_array($this->request['node']->wlDevice)){
			$node->updateWlInterface($dataNode->wlDevice);
		}
		// update rf links
		if(is_array($dataNode->rfNeighbour)){
			$node->updateRfLink($dataNode->rfNeighbour);
		}
		// update rp links
		if(is_object($dataNode->neighbour)){
			$node->updateRpLink($dataNode->neighbour);
		}
		$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
		$this->response->setResponseMsg('Operation Succeded.');
		return $this->response;
	}
}
?>