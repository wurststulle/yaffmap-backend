<?php
class YaffmapGetID extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('macAddr', 'ipv4Addr', 'ipv6Addr', 'addressSet');
		$this->checkInput($allowed);
	}
	
	/**
	 * 
	 * @return YaffmapResponse
	 * @throws EAddrMissing
	 */
	public function getID(){
		$addressSets = Yaffmap::decodeJson($this->request['addressSet']);
		$addrMapNode = null;
		$node = null;
		foreach($addressSets as $addressSet){
			$macAddr = ((isset($addressSet->macAddr))?strtolower($addressSet->macAddr):null);
			$ipv4Addr = ((isset($addressSet->ipv4Addr))?strtolower($addressSet->ipv4Addr):null);
			$ipv6Addr = ((isset($addressSet->ipv6Addr))?strtolower($addressSet->ipv6Addr):null);
			// alle kombinationen testen!!!!
			if($ipv6Addr != null){
				// only ipv6 address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByIpv4addr($ipv4Addr)->findOne();
			}elseif($ipv4Addr != null){
				// only ipv4 address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByIpv4addr($ipv4Addr)->findOne();
			}elseif($macAddr != null){
				// only mac address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByMacAddr($macAddr)->findOne();
			}else{
				throw new EAddrMissing();
			}
			if($addrMapNode != null){
				// node found
				$node = $addrMapNode->getFfNode();
				if($node->getIsGlobalUpdated()){
					/* 
					 * if nodewas inserted by global update, a new ID has to be 
					 * generated because of nodeUpdate is updating nodes/interfaces 
					 * inserted by global update 
					 */
					$node = new FfNode();
					$node->setId(md5(mt_rand(1, 1000).$macAddr.date("U")));
					$node->save();
				}
				break;
			}
		}
		if($node == null){
			// node not found, create new
			$node = new FfNode();
			$node->setId(md5(mt_rand(1, 1000).$macAddr.date("U")));
			$node->save();
		}
		$this->response->setErrorCode(YaffmapResponse::OPERATION_SUCCEDED);
		$this->response->setErrorMsg('Operation Succeded.');
		$this->response->addData('id="'.$node->getId().'"');
		return $this->response;
	}
}
?>