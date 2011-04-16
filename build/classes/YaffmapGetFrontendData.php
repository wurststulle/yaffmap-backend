<?php
class YaffmapGetFrontendData extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
//		$allowed = array();
//		$this->checkInput($allowed);
	}
	
	public function getFrontendData(){
		// delete old data
//		AddrMapQuery::deleteOld($this->config->deleteOld['addrMap']);
//		FfNodeQuery::deleteOld($this->config->deleteOld['ffNode']);
//		RpLinkQuery::deleteOld($this->config->deleteOld['rpLink']);
//		RfLinkQuery::deleteOld($this->config->deleteOld['rfLink']);
//		RfLinkOneWayQuery::deleteOld($this->config->deleteOld['rfLinkOneWay']);
		
		$request = Yaffmap::decodeJson('{"ul":"52.762709,12.1234","lr":"48.1234,16.1234","node":["latitude","longitude","id","hostname"],"rpLinkFilter": {
	         "cost":[["<","10"],[">=", "1"]],"rp":[{"type":"olsr","metrics":["etx_ff"]}]},
			"rpLink":
	     [
	      "cost",
	      "rp",
	      "metric"
	   ]}');
//			$request = Yaffmap::decodeJson($_REQUEST['request']);
		$responseObj = new stdClass();
		$ul = explode(',', $request->ul);
		$lr = explode(',', $request->lr);
		$nodes = FfNodeQuery::create()
			->condition('c1', 'FfNode.Latitude <= ?', $ul[0])
			->condition('c2', 'FfNode.Latitude >= ?', $lr[0])
			->combine(array('c1', 'c2'), 'and', 'c12')
			->condition('c3', 'FfNode.Longitude >= ?', $ul[1])
			->condition('c4', 'FfNode.Longitude <= ?', $lr[1])
			->combine(array('c3', 'c4'), 'and', 'c34')
			->where(array('c12', 'c34'), 'and')
			->find();
		$rpLinks = null;
		$rpLinkOutputFilter = null;
		foreach($request as $requestKey => $requestVal){
			if($requestKey == 'ul' OR $requestKey == 'lr'){
				continue;
			}
			switch($requestKey){
				case 'node':
					foreach($nodes as $node){
						$item = null;
						foreach($requestVal as $attr){
							try{
								$item->$attr = call_user_func(array($node, 'get'.ucfirst($attr)));
							}catch(Exception $e){
								throw new EUnknownRequestElement($attr);
							}
						}
						$responseObj->node[] = $item;
					}
					break;
				case 'rpLinkFilter':
					$rpLinksQuery = RpLinkLocationQuery::create();
					foreach($requestVal as $rpLinkAttrKey => $rpLinkAttrVal){
						switch($rpLinkAttrKey){
							case 'cost':
								// filter by cost
								foreach($rpLinkAttrVal as $costFilter){
									try{
										$rpLinksQuery->where('RpLinkLocation.Cost '.$costFilter[0].' ?', $costFilter[1]);
									}catch(Exception $e){
										throw new EUnknownRequestElement('RpLinkLocation.Cost '.$costFilter[0].' '.$costFilter[1]);
									}
								}
							break;
							case 'rp':
								foreach($rpLinkAttrVal as $elem){
									foreach($elem as $key => $val){
										switch($key){
											case 'type':
												// filter by routing protocol name
												$rpLinksQuery->filterByRp($val);
												break;
											case 'metrics':
												// filter by metric
												foreach($val as $metric){
													$rpLinksQuery->filterByMetric($metric);
												}
												break;
											default:
												throw new EUnknownRequestElement($key);
										}
									}
								}
							break;
							default:
								throw new EUnknownRequestElement($rpLinkAttrKey);
						}
					}
					$rpLinks = $rpLinksQuery->find();
					break;
				case 'rpLink':
					$rpLinkOutputFilter = $requestVal;
					break;
				case 'ipvFilter':
					// TODO
					break;
				case 'ipv':
					// TODO
					break;
				case 'wlDevice':
					// TODO
					break;
				case 'wlDeviceFilter':
					// TODO
					break;
				case 'wlIface':
					// TODO
					break;
				case 'wlIfaceFilter':
					// TODO
					break;
				default:
					throw new EUnknownRequestElement($requestKey);
			}
		}
		$responseObj->rpLink = array();
		foreach($rpLinks as $rpLink){
			$responseObjNode = new StdClass();
			foreach($rpLinkOutputFilter as $output){
				$responseObjRpLink->$output = call_user_func(array($rpLink, 'get'.ucfirst($output)));
			}
			$responseObjRpLink->sourceLat = $rpLink->getSourceLat();
			$responseObjRpLink->sourceLon = $rpLink->getSourceLon();
			$responseObjRpLink->destLat = $rpLink->getDestLat();
			$responseObjRpLink->destLon = $rpLink->getDestLon();
			$responseObj->rpLink[] = $responseObjRpLink;
			$responseObjRpLink = null;
		}
		return json_encode($responseObj);
	}
}
?>