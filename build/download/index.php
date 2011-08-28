<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';

if(isset($_REQUEST['getFile'])){
	$agent = AgentReleaseQuery::create()->findOneById($_REQUEST['getFile']);
	/* @var $agent AgentRelease */
	if($agent != null){
		// TODO
//		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//		header("Content-Length: " . filesize($filename));
		header("Content-type: application/force-download");
		header('Content-Disposition: attachment; filename='.'yaffmap_'.$agent->getRelease().'-'.$agent->getSubRelease().'_'.$agent->getVersion().'_'.$agent->getUpgradeTree().'.tar.gz');
		$agent->setDlCount($agent->getDlCount()+1);
		$agent->save();
		echo stream_get_contents($agent->getAgent());
	}else{
		echo 'file not found!';
	}
}else{
	$agents = AgentReleaseQuery::create()->orderByVersion()->orderByUpgradeTree()->orderBySubRelease()->find();
	$table = new KTable();
	$table->addAttribute(array(new KAttribute('border', 1)));
	$table->addThRow(array('version', 'tree', 'release', 'release date', 'download'));
	foreach($agents as $agent) {
		/* @var $agent AgentRelease */
		$table->addRow(array($agent->getVersion(), $agent->getUpgradeTree(), $agent->getRelease().'-'.$agent->getSubRelease(), $agent->getReleaseDate(), new KSimpleLink('index.php?getFile='.$agent->getId(), 'download')));
	}
	echo $table;
}
?>