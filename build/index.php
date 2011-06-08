<?php
require_once 'propel/Propel.php';
Propel::init("conf/yaffmap-conf.php");
set_include_path("classes".PATH_SEPARATOR.get_include_path());
include dirname(__FILE__) . '/classes/Autoloader.php';

define('DEBUG', true);

//if($_SERVER['REMOTE_ADDR'] == '192.168.2.10' || $_SERVER['REMOTE_ADDR'] == '192.168.2.21'){
//	
//}else{
//	$response = new Response();
//	$response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
//	$response->setErrorMsg('server is under maintenance.');
//	echo $response;
//	die();
//}

//function exceptions_error_handler($severity, $message, $filename, $lineno){
//	if(error_reporting() == 0){
//    	return;
//	}
//	if(error_reporting() & $severity){
//		throw new YaffmapException($message);
//	}
//}
//set_error_handler('exceptions_error_handler'); 

if(DEBUG){
	error_reporting(E_ALL & ~E_NOTICE);
	ini_set('display_errors','On');
}

try{
	if(isset($_REQUEST['do'])){
		switch($_REQUEST['do']){
			case 'getID':
				$getID = new YaffmapGetID();
				echo $getID->getID();
				break;
			case 'update':
				$update = new YaffmapNodeUpdate(Yaffmap::decodeJson($_REQUEST['node']));
				echo $update->nodeUpdate($_REQUEST['version'], $_REQUEST['tree'], $_REQUEST['release']);
				break;
			case 'globalUpdate':
				$update = new YaffmapGlobalUpdate();
				echo $update->globalUpdate();
				break;
			case 'getUpgrade':
				$getUpgrade = new YaffmapGetUpgrade();
				echo $getUpgrade->getUpgrade();
				break;
			case 'getFilters':
				$filter = new YaffmapGetFilter();
				echo $filter->getFilter();
				break;
			case 'getFrontendData':
				$data = new YaffmapGetFrontendData();
				echo $data->getFrontendData();
				break;
			case 'newAgentReleaseWithFile':
				// Yaffmap::logAccess();
				$new = new YaffmapNewAgentRelease();
				echo $new->newAgentReleaseWithFile();
				break;
			case 'debugUpload':
				// upload file
				$CHUNK = 8192;
				if (!($putData = fopen("php://input", "r"))){
					throw new YaffmapLoggedException("Can't get PUT data."); 
				}
				$tot_write = 0;
				@mkdir('download/debug', 0755, true);
	            $destFile = 'download/debug/'.date('Y-m-d H:i:s').'.tar.gz'; 
	            if(!is_file($destFile)){
	                fclose(fopen($destFile, "x"));
	                if(!($fp = fopen($destFile, "w"))){
	                	throw new YaffmapLoggedException("Can't write to file");
	                }
	                while($data = fread($putData, $CHUNK)){
	                    $chunk_read = strlen($data);
	                    if(($block_write = fwrite($fp, $data)) != $chunk_read){
	                    	throw new YaffmapLoggedException("Can't write more to file");
	                    }
	                    $tot_write += $block_write;
	                }
	                if(!fclose($fp)){
	                	throw new YaffmapLoggedException("Can't close file");
	                }
	                fclose($putData);
	                unset($putData);
	            }
				break;
			case 'announce':
				// http://192.168.2.3/replication/build/index.php?do=announce&url=http://192.168.2.201/yaffmap/build
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->announce($_GET['url']);
				}else{
					$backend->announce();
				}
				break;
			case 'getBackends':
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->getBackends($_GET['url']);
				}else{
					$backend->getBackends();
				}
				break;
			case 'getAgentRelease':
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->getAgentRelease($_GET['url']);
				}else{
					$backend->getAgentRelease();
				}
				
				break;
			case 'knock':
				$response = new YaffmapResponse();
				$response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
				$response->setResponseMsg('Yaffmap Backend v'.YaffmapConfig::get('version'));
				echo $response;
				break;
			case 'getVersionMappingAgent':
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->getVersionMappingAgent($_GET['url']);
				}else{
					$backend->getVersionMappingAgent();
				}
				break;
			case 'getVersionMappingBackend':
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->getVersionMappingBackend($_GET['url']);
				}else{
					$backend->getVersionMappingBackend();
				}
				break;
			case 'replicateEnv':
				$response = new YaffmapResponse();
				$response->setResponseCode(YaffmapResponse::OPERATION_SUCCEDED);
				$backend = new YaffmapBackend();
				if(isset($_GET['url'])){
					$backend->getVersionMappingBackend($_GET['url']);
					$backend->getVersionMappingAgent($_GET['url']);
					$response->setResponseMsg($backend->getAgentRelease($_GET['url']));
					$response->setResponseMsg($backend->getBackends($_GET['url']));
				}else{
					$backend->getVersionMappingBackend();
					$backend->getVersionMappingAgent();
					$response->setResponseMsg($backend->getAgentRelease());
					$response->setResponseMsg($backend->getBackends());
				}
				echo $response;
				break;
			case 'replicateNodes':
				$backend = new YaffmapBackend();
				$backend->replicateNodes('http://yaffmap.gross-holger.de');
				break;
			case 'test':
				break;
			default:
				throw new EUnknownRequestElement($_REQUEST['do']);
		}
	}else{
		$div = new KDiv();
		$div->addAttribute(array(new KAttribute('align', 'center')));
		$div->addItem('<font color="red">Y</font>et <font color="red">A</font>nother <font color="red">F</font>rei<font color="red">f</font>unk <font color="red">Map</font> Backend');
		$div->addItem(array(new KHr().new KBr()));
		$div->addItem('see '.new KSimpleLink('http://wurststulle.dyndns.org/yaffmap/trac', 'documentation').' for help');
		$div->addItem(new KBr());
		$div->addItem(new KBr());
		$table = new KTable();
		$table->addAttribute(array(new KAttribute('border', 1)));
		$table->addThRow(array('Knoten: ', FfNodeQuery::create()->count()));
		$table->addRow(array('- davon mit Agent', FfNodeQuery::create()->filterByAgentRelease(null, Criteria::NOT_EQUAL)->count()));
		$table->addRow(array('- davon mit Koordinaten', FfNodeQuery::create()->filterByLatitude(null, Criteria::NOT_EQUAL)->count()));
		$table->addRow(array('- davon ohne Koordinaten', FfNodeQuery::create()->filterByLatitude(null)->count()));
		$table->addThRow(array('RP Links: ', RpLinkQuery::create()->count()));
		$rpLinks = RpQuery::create()->joinRpLink()->groupByIpv()->withColumn('count('.RpPeer::IPV.')', 'CountIpv')->find();
		foreach($rpLinks as $t){
			$table->addRow(array('- davon '.$t->getName().' (IPv'.$t->getIpv().')', $t->getCountIpv()));
		}
		$div->addItem($table);
		echo $div;
	}
}catch(PropelException $e){
	$error = new ErrorLog();
	$error->setRequest(Yaffmap::dump_ret($_REQUEST));
	$error->setMessage($message);
	$error->setIp($_SERVER['REMOTE_ADDR']);
	$error->setType(ErrorLogPeer::TYPE_PROPEL);
	$error->save();
	$response = new YaffmapResponse();
	$response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
	$response->setErrorMsg('Propel Exception: '.$e);
	echo $response;
}catch(YaffmapSoapException $e){
	$response = new YaffmapResponse();
	$response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
	$response->setErrorMsg('SOAP Exception: '.$e->getMessage());
	echo $response;
}catch(YaffmapException $e){
	$response = new YaffmapResponse();
	$response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
	if(!DEBUG){
		$response->setErrorMsg('Yaffmap Exception: '.$e->getMessage());
	}else{
		$response->setErrorMsg('Yaffmap Exception: '.$e);
	}
	echo $response;
}catch(Exception $e){
	$error = new ErrorLog();
	$error->setRequest(Yaffmap::dump_ret($_REQUEST));
	$error->setMessage($message);
	$error->setIp($_SERVER['REMOTE_ADDR']);
	$error->setType(ErrorLogPeer::TYPE_EXCEPTION);
	$error->save();
	$response = new YaffmapResponse();
	$response->setErrorCode(ResponseCodeNode::OPERATION_FAILED);
	$response->setErrorMsg('General Exception: '.$e);
	echo $response;
}
?>
