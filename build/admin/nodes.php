<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/KoboldTemplate.php';

$nodes = FfNodeQuery::create()
	->filterByAgentRelease(null, Criteria::NOT_EQUAL)
	->find();

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
foreach($nodes as $node){
	/* @var $node FfNode */
	$table->addRow(array($node->getHostname(), $node->getAgentRelease(), $node->getUpdatedAt(), new KSimpleLink('nodeDetail.php?id='.$node->getId(), 'detail')));
}

echo $table;
?>