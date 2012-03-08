<?php
define('DEBUG', true);
if(DEBUG){
	//error_reporting(E_ALL & ~E_NOTICE);
	error_reporting(E_ALL);
	ini_set('display_errors','On');
}

require_once 'classes/YaffmapException.php';
require_once 'propel/Propel.php';
Propel::init("conf/yaffmap-conf.php");
set_include_path("classes".PATH_SEPARATOR.get_include_path());

class Autoloader{
	public static function load($classname){
		include $classname.'.php';
	}
}
spl_autoload_register('Autoloader::load');

include dirname(__FILE__) . '/vendor/vendor.php';

//if($_SERVER['REMOTE_ADDR'] == '192.168.2.10' || $_SERVER['REMOTE_ADDR'] == '192.168.2.21'){
//
//}else{
//	$response = new Response();
//	$response->setErrorCode(YaffmapResponse::OPERATION_FAILED);
//	$response->setErrorMsg('server is under maintenance.');
//	echo $response;
//	die();
//}

try{
	if(isset($_REQUEST['do'])){
		switch($_REQUEST['do']){
			case 'getID':
				$node = FfNode::findOneByAddrSet($_REQUEST['addressSet']);
				$response = new YaffmapResponse(YaffmapResponse::OPERATION_SUCCEDED, 'Operation Succeded.', 'id="'.$node->getId().'"');
				echo $response;
			break;
			case 'update':
				$nodeData = $_REQUEST['node'];
				$node = FfNode::getNodeById($nodeData->id);
				if($node == null){
					// node was not found in database, tell agent to run getID
					$response = new YaffmapResponse(YaffmapResponse::NODEID_NOT_FOUND, 'NodeID not found, please run getID.');
					echo $response;
				}
				$node->updateNode($nodeData, $nodeData->version, $nodeData->tree, $nodeData->release);
				$response = new YaffmapResponse(YaffmapResponse::OPERATION_SUCCEDED, 'Operation Succeded.', 'id="'.$node->getId().'"');
				echo $response;
			break;
			case 'globalUpdate':
				$update = new YaffmapGlobalUpdate();
				echo $update->globalUpdate();
				break;
			case 'getUpgrade':
				$getUpgrade = new YaffmapGetUpgrade();
				echo $getUpgrade->getUpgrade();
				break;
			case 'newAgentReleaseWithFile':
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
					$response->appendResponseMsg($backend->getBackends($_GET['url']));
				}else{
					$backend->getVersionMappingBackend();
					$backend->getVersionMappingAgent();
					$response->setResponseMsg($backend->getAgentRelease());
					$response->appendResponseMsg($backend->getBackends());
				}
				echo $response;
				break;
			case 'checkPost':
				// returns value of posted echo key
				// TODO not implemented in agent
				if(isset($_POST['echo'])){
					echo $_POST['echo'];
				}
				break;
			case 'ping':
				echo 'Yaffmap backend v'.YaffmapConfig::get('version');
				break;
			case 'replicateNodes':
				$backend = new YaffmapBackend();
				$backend->replicateNodes();
				break;
			case 'test':
				echo Kobold::dump(Yaffmap::decodeJson('[{"macAddr":"00:1D:0F:A5:48:39","ipv4Addr":"104.13.5.1"},{"macAddr":"00:1D:0F:A5:48:39","ipv4Addr":"104.13.5.1"}]'));
				break;
			default:
				throw new EUnknownRequestElement($_REQUEST['do']);
		}
	}else{
		$div = new KDiv();
		$div->addAttribute(array(new KAttribute('align', 'center')));
		$div->addItem('<font color="red">Y</font>et <font color="red">A</font>nother <font color="red">F</font>rei<font color="red">f</font>unk <font color="red">Map</font> Backend v'.YaffmapConfig::get('version'));
		$div->addItem(array(new KHr().new KBr()));
		$div->addItem('see '.new KSimpleLink('https://github.com/wurststulle/yaffmap-backend', 'github').' for help');
		$div->addItem(new KBr());
		$div->addItem(new KBr());
		$table = new KTable();
		$table->addAttribute(array(new KAttribute('border', 1)));
		$table->addThRow(array('Knoten: ', FfNodeQuery::create()->count().' '));
		$table->addRow(array('- davon mit Agent', FfNodeQuery::create()->filterByAgentRelease(null, Criteria::NOT_EQUAL)->count().' '));
		$table->addRow(array('- davon mit Koordinaten', FfNodeQuery::create()->filterByLatitude(null, Criteria::NOT_EQUAL)->count().' '));
		$table->addRow(array('- davon ohne Koordinaten', FfNodeQuery::create()->filterByLatitude(null)->count().' '));
		$table->addThRow(array('RP Links: ', RpLinkQuery::create()->count().' '));
		$rpLinks = RpQuery::create()->joinRpLink()->groupByIpv()->withColumn('count('.RpPeer::IPV.')', 'CountIpv')->find();
		if(!is_null($rpLinks)){
			foreach($rpLinks as $t){
				/* @var $t RpLink */
				$table->addRow(array('- davon '.$t->getName().' (IPv'.$t->getIpv().')', $t->getVirtualColumn('CountIpv').' '));
			}
		}
		$table->addThRow(array('RF Links: ', RfLinkQuery::create()->count().' '));
		$table->addThRow(array('RF OneWay Links: ', RfLinkOneWayQuery::create()->count().' '));
		$div->addItem($table);
		echo $div;
	}
}catch(PropelException $e){
	$error = new ErrorLog();
	$error->setRequest(Kobold::dump_ret($_REQUEST));
	$error->setMessage($message);
	$error->setIp($_SERVER['REMOTE_ADDR']);
	$error->setType(ErrorLogPeer::TYPE_PROPEL);
	$error->save();
	$response = new YaffmapResponse();
	$response->setResponseCode(YaffmapResponse::OPERATION_FAILED);
	$response->setResponseMsg('Propel Exception: '.$e);
	echo $response;
}catch(YaffmapSoapException $e){
	$response = new YaffmapResponse();
	$response->setResponseCode(YaffmapResponse::OPERATION_FAILED);
	$response->setResponseMsg('SOAP Exception: '.$e->getMessage());
	echo $response;
}catch(YaffmapException $e){
	$response = new YaffmapResponse();
	$response->setResponseCode(YaffmapResponse::OPERATION_FAILED);
	if(!DEBUG){
		$response->setResponseMsg('Yaffmap Exception: '.$e->getMessage());
	}else{
		$response->setResponseMsg('Yaffmap Exception: '.$e);
	}
	echo $response;
}catch(Exception $e){
	$error = new ErrorLog();
	$error->setRequest(Kobold::dump_ret($_REQUEST));
	$error->setMessage($message);
	$error->setIp($_SERVER['REMOTE_ADDR']);
	$error->setType(ErrorLogPeer::TYPE_EXCEPTION);
	$error->save();
	$response = new YaffmapResponse();
	$response->setResponseCode(YaffmapResponse::OPERATION_FAILED);
	$response->setResponseMsg('General Exception: '.$e);
	echo $response;
}
?>
