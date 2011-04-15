<?php
class YaffmapGetFilter extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
//		$allowed = array();
//		$this->checkInput($allowed);
	}
	
	public function getFilter(){
		$node = '"node":{"id":[], "hostname":[], "latitude":[], "longitude":[], "isGlobalupdated":["true", "false"]}';
		$wlStandards = WlDeviceQuery::create()->groupByWirelessStandard()->find();
		$items = 0;
		foreach($wlStandards as $wlStandard){
			if($wlStandard->getWirelessStandard() != null){
				if($items != 0){
					$wlStand .= ',';
				}
				$wlStand .= '"'.$wlStandard->getWirelessStandard().'"';
				$items++;
			}
		}
		$wlFrequencys = WlDeviceQuery::create()->groupByFrequency()->find();
		$items = 0;
		foreach($wlFrequencys as $wlFrequency){
			if($wlFrequency->getFrequency() != null){
				if($items != 0){
					$frequencys .= ',';
				}
				$frequencys .= '"'.$wlFrequency->getFrequency().'"';
				$items++;
			}
		}
		$wlDevice = '"wlDevice":{"wirelessStandard":['.$wlStand.'], "frequency":['.$frequencys.']}';
		$wlModes = WlIfaceQuery::create()->groupByWlMode()->find();
		$items = 0;
		foreach($wlModes as $wlMode){
			if($wlMode->getWlMode() != null){
				if($items != 0){
					$wlModeArr .= ',';
				}
				$wlModeArr .= '"'.$wlMode->getWlMode().'"';
				$items++;
			}
		}
		$wlIface = '"wlIface":{"wlMode":['.$wlModeArr.']}';
		$rps = RpQuery::create()->groupByName()->find();
		$items = 0;
		foreach($rps as $rp){
			$metricse = MetricTypeQuery::create()->filterByRp($rp)->groupByName()->find();
			$metricArr = '';
			$items1 = 0;
			foreach($metricse as $metric){
				if($items1 != 0) $metricArr .= ',';
				$metricArr .= '"'.$metric->getName().'"';
				$items1++;
			}
			if($items != 0) $rpArr .= ',';
			$rpArr .= '{"name":"'.$rp->getName().'",';
			$rpArr .= '"metrics":['.$metricArr.']}';
			$items++;
		}
		$rp = '"rp":['.$rpArr.']';
		$ipv = '"ipv":["0", "4", "6", "46"]';
		$filters = '{'.$node.','.$wlDevice.','.$wlIface.','.$rp.','.$ipv.'}';
		return $filters;
	}
}
?>