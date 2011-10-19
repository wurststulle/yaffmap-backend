<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../vendor/vendor.php';
require_once '../vendor/PHPLiveX/PHPLiveX.php';
require_once 'YaffmapAdmin.php';
require_once 'YaffmapConfig.php';

switch($_REQUEST['do']){
	case 'deleteAgentMap':
		VersionMappingAgentQuery::create()->findPk($_REQUEST['id'])->delete();
		break;
	case 'deleteBackendMap':
		VersionMappingBackendQuery::create()->findPk($_REQUEST['id'])->delete();
		break;
	case 'addAgentMap':
		$agentRelease = AgentReleaseQuery::create()->findPk($_REQUEST['agentMap']);
		$agentMap = VersionMappingAgentQuery::create()
			->filterByAgentRelease($agentRelease->getRelease())
			->filterByAgentSubRelease($agentRelease->getSubRelease())
			->filterByAgentVersion($agentRelease->getVersion())
			->filterByAgentUpgradeTree($agentRelease->getUpgradeTree())
			->filterByBackendRelease($_REQUEST['backendMap'])
			->findOneOrCreate();
		if($agentMap->isNew()){
			$agentMap->save();
		}
		break;
	case 'addBackendMap':
		$backendMap = VersionMappingBackendQuery::create()
			->filterByClientRelease($_REQUEST['backendMap'])
			->filterByServerRelease($_REQUEST['backendMapServer'])
			->findOneOrCreate();
		if($backendMap->isNew()){
			$backendMap->save();
		}
		break;
}

$mappingAgentRelease = VersionMappingAgentQuery::create()->orderByAgentRelease(Criteria::DESC)->orderByAgentSubRelease(Criteria::DESC)->find();
if($mappingAgentRelease != null){
	$table = new KTable();
	$table->addThRow(array('agent', new KBlanc(), 'backend', new KBlanc()));
	$table->addAttribute(array(new KAttribute('border', 1)));
	foreach($mappingAgentRelease as $mapping){
		$table->addRow(array($mapping->getAgentRelease().'-'.$mapping->getAgentSubRelease().'_'.$mapping->getAgentVersion().'-'.$mapping->getAgentUpgradeTree(), '<->',$mapping->getBackendRelease(),
		new KSimpleLink('mapping.php?do=deleteAgentMap&id='.$mapping->getId(), 'remove')));
	}
}
echo $table;
$mappingBackend = VersionMappingBackendQuery::create()->find();
if($mappingBackend != null){
	$table = new KTable();
	$table->addAttribute(array(new KAttribute('border', 1)));
	$table->addThRow(array('client', new KBlanc(), 'server', new KBlanc()));
	foreach($mappingBackend as $mapping){
		$table->addRow(array($mapping->getClientRelease(), '-->', $mapping->getServerRelease(),
		new KSimpleLink('mapping.php?do=deleteBackendMap&id='.$mapping->getId(), 'remove')));
	}
}

echo $table;
$agentMap = new KSelect();
$agentMap->addAttribute(array(new KAttributeName('agentMap')));
$agentRelease = AgentReleaseQuery::create()->orderByRelease(Criteria::DESC)->orderBySubRelease(Criteria::DESC)->find();
foreach($agentRelease as $release){
	$agentMap->addOption($release->getId(), $release->getRelease().'-'.$release->getSubRelease().'_'.$release->getVersion().'-'.$release->getUpgradeTree());
}

$backendMap = new KSelect();
$backendMap->addAttribute(array(new KAttributeName('backendMap')));
$backendRelease = VersionMappingAgentQuery::create()->groupByBackendRelease()->orderByBackendRelease(Criteria::DESC)->find();
$currVersionExisting = false;
foreach($backendRelease as $release){
	if($release->getBackendRelease() == YaffmapConfig::get('version')){
		$currVersionExisting = true;
	}
	$backendMap->addOption($release->getBackendRelease(), $release->getBackendRelease());
}
if(!$currVersionExisting){
	$backendMap->addOption(YaffmapConfig::get('version'), YaffmapConfig::get('version'));
}

$backendMapServer = clone $backendMap;
$backendMapServer->addAttribute(array(new KAttributeName('backendMapServer')));
$form = new KForm();
$form->addAttribute(array(new KAttribute('method', 'post'), new KAttribute('action', 'mapping.php?do=addAgentMap')));
$btn = new KButton();
$form->addItem(array('agent to backend mapping: '.$agentMap,'<->', $backendMap, $btn, new KBr()));
echo $form;

$form = new KForm();
$form->addAttribute(array(new KAttribute('method', 'post'), new KAttribute('action', 'mapping.php?do=addBackendMap')));
$btn = new KButton();
$form->addItem(array('backend to backend mapping: '.$backendMap,'(client)-->', $backendMapServer, '(server)', $btn, new KBr()));
echo $form;
?>