<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/KoboldTemplate.php';
if($_REQUEST['with'] == 'warnings'){
	$errors = ErrorLogQuery::create()->find();
}else{
	$errors = ErrorLogQuery::create()->filterByType('exception')->find();
}

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
foreach($errors as $error){
	$tr1 = $table->addThRow(array("id", "errorMsg", "ip", "createdAt", "request"));
	$tr1->addAttribute(array(new KAttributeStyleBackground('lightgreen')));
	$table->addRow(array($error->getId(), $error->getMessage(), $error->getIp(),$error->getCreatedAt(), $error->getRequest()));
}
echo $table;
?>