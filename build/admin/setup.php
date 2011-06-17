<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';
require_once '../classes/YaffmapConfig.php';

function check($var){
	if($var){
		return 'ok.<br>';
	}else{
		return '<font color="red">failed</font>.<br>';
	}
}

if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'setConfig'){
	// TODO save
}

echo 'checking for soap... '.check(in_array('soap', get_loaded_extensions()));
echo 'checking for json... '.check(in_array('json', get_loaded_extensions()));
echo 'checking for Reflection... '.check(in_array('Reflection', get_loaded_extensions()));
echo 'checking for config... '.check($config = ConfigQuery::create()->findOne() != null);
echo 'base dir writeable... '.check(is_writeable('../../'));
echo '<br><br>';
echo 'configuration:'.new KBr();
$form = new KForm(array(new KAttribute('method', 'post'), new KAttribute('action', 'setup.php?do=setConfig')));
$table = new KTable(array(new KAttribute('border', '1')));
$table->addThRow(array('key', 'value'));
$table->addRow(array('url', new KInputfield(array(new KAttributeName('url'), new KAttributeId('url'), new KAttributeValue(YaffmapConfig::get('url')), new Kattribute('size', '50')))));
$table->addRow(array('defaultTree', new KInputfield(array(new KAttributeName('defaultTree'), new KAttributeId('defaultTree'), new KAttributeValue(YaffmapConfig::get('defaultTree')), new Kattribute('size', '50')))));
$tr = $table->addRow(array(new KTd(array(new KAttribute('colspan', '2')), array(new KButton(array(new KAttributeValue('save')))))));
$form->addItem($table);
echo $form;
?>