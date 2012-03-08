<?php
/**
 * @deprecated
 * Enter description here ...
 * @author wurst
 *
 */
class YaffmapGetID extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('addressSet');
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
// 		if(count($addressSets) == 0){
// 			throw new EAddrMissing();
// 		}else{
// 			$addrMapNode = AddrMapNodeQuery::create();
// 			$numElements = count($addressSets);
// 			$i = 0;
// 			foreach($addressSets as $addressSet){
// 				if($i != 0){
// 					$addrMapNode->_or();
// 				}
// 				$addrMapNode
// 					->_if(isset($addressSet->macAddr))
// 						->filterByMacAddr($addressSet->macAddr)
// 					->_endif()
// 					->_if(isset($addressSet->ipv4Addr))
// 						->filterByIpv4addr($addressSet->ipv4Addr)
// 					->_endif()
// 					->_if(isset($addressSet->ipv6Addr))
// 						->filterByIpv6addr($addressSet->ipv6Addr)
// 					->_endif();
// 				$i++;
// 			}
// 			$addrMapNode->find();
// 			if($addrMapNode->size() == 1){
// 				$node = $addrMapNode->getFirst()->getFfNode();
// 				if($node->getIsGlobalUpdated()){
// 					$node = new FfNode();
// 					$node->setId(md5(mt_rand(1, 1000).$macAddr.date("U")));
// 					$node->save();
// 				}
// 			}
// 			if($node == null){
// 				$node = new FfNode();
// 				$node->setId(md5(mt_rand(1, 1000).$macAddr.date("U")));
// 				$node->save();
// 			}
// 			$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
// 			$this->response->setResponseMsg('Operation Succeded.');
// 			$this->response->addResponseData('id="'.$node->getId().'"');
// 			return $this->response;
// 		}
		foreach($addressSets as $addressSet){
			
			$macAddr = ((isset($addressSet->macAddr))?strtolower($addressSet->macAddr):null);
			$ipv4Addr = ((isset($addressSet->ipv4Addr))?strtolower($addressSet->ipv4Addr):null);
			$ipv6Addr = ((isset($addressSet->ipv6Addr))?strtolower($addressSet->ipv6Addr):null);
// 			alle kombinationen testen!!!!
			if($ipv6Addr != null){
// 				only ipv6 address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByIpv4addr($ipv4Addr)->findOne();
			}elseif($ipv4Addr != null){
// 				only ipv4 address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByIpv4addr($ipv4Addr)->findOne();
			}elseif($macAddr != null){
// 				only mac address given
				$addrMapNode = AddrMapNodeQuery::create()->filterByMacAddr($macAddr)->findOne();
			}else{
				throw new EAddrMissing();
			}
			if($addrMapNode != null){
// 				node found
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
// 			node not found, create new
			$node = new FfNode();
			$node->setId(md5(mt_rand(1, 1000).$macAddr.date("U")));
			$node->save();
		}
		$this->response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
		$this->response->setResponseMsg('Operation Succeded.');
		$this->response->addResponseData('id="'.$node->getId().'"');
		return $this->response;
	}
}
?>