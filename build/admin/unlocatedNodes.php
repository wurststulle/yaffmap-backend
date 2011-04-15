<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/KoboldTemplate.php';

switch($_REQUEST['do']){
	case 'getNode':
		$node = FfNodeQuery::create()->filterById($_REQUEST['node'])->findOne();
		$form = new KForm(array('id' => 'myForm', 'action' => 'unlocatedNodes.php?do=updateNode', 'method' => 'post'));
		$table = new KTable(array(new KAttribute('border', 1)));
		$td = new KTd(array('Change geo coordinates for '.$node->getHostName()));
		$td->addAttribute(array(new KAttributeColspan(2)));
		$table->addRow(array($td));
		$latitude = new KTextfield(array(new KAttributeId('latitude'), new KAttributeName('latitude')));
		$id = new KHiddenfield(array(new KAttributeId('node'), new KAttributeName('node'), new KAttributeValue($_REQUEST['node'])));
		$table->addRow(array('latitude', $latitude.$id));
		$longitude = new KTextfield(array(new KAttributeId('longitude'), new KAttributeName('longitude')));
		$table->addRow(array('longitude', $longitude));
		
		$hostname = new KTextfield(array(new KAttributeId('hostname'), new KAttributeName('hostname'), new KAttributeValue($node->getHostName())));
		$table->addRow(array('hostname', $hostname));
		
		$td = new KTd(array(new KButton()));
		$td->addAttribute(array(new KAttributeColspan(2)));
		$table->addRow(array($td));
		$form->addItem($table);
	echo $form;
		break;
	case 'updateNode':
		$node = FfNodeQuery::create()->filterById($_REQUEST['node'])->findOne();
		$node->setLatitude((empty($_REQUEST['latitude'])?null:$_REQUEST['latitude']));
		$node->setLongitude((empty($_REQUEST['longitude'])?null:$_REQUEST['longitude']));
		$node->setHostName((empty($_REQUEST['hostname'])?null:$_REQUEST['hostname']));
		$node->save();
		echo 'Node updated, <a href="index.php">click here</a>';
		break;
	default:
		$nodes = UnlocatedNodesQuery::create()->orderByIpaddr()->find();
		$form = new KForm(array('id' => 'myForm', 'action' => 'unlocatedNodes.php?do=getNode', 'method' => 'post'));
		$tbl = new KTable(array(new KAttribute('border', 1)));
		foreach($nodes as $node){
			$n = FfNodeQuery::create()->filterById($node->getNodeID())->findOne();
			$tbl->addRow(array(new KSimpleLink('nodeDetail.php?id='.$node->getNodeID(), $node->getHostname()), new KSimpleLink('http://'.$node->getIpAddr(), $node->getIpAddr()), new KRadioButton(array(new KAttributeName('node'), new KAttributeId($node->getNodeID()), new KAttributeValue($node->getNodeID())))));
		}
		$td = new KTd(array(new KButton()));
		$td->addAttribute(array(new KAttributeColspan(2)));
		$tr = $tbl->addRow(array($td));
		$form->addItem($tbl);
		echo $form;
}
?>