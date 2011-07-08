<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';
require_once '../classes/YaffmapConfig.php';

function check($var, $out = null){
	if($var){
		if($out == null){
			return '<font color="green">ok</font>.<br>';
		}else{
			return '<font color="green">'.$out.'</font>.<br>';
		}
	}else{
		if($out == null){
			return '<font color="red">failed</font>.<br>';
		}else{
			return '<font color="red">'.$out.'</font>.<br>';
		}
		
	}
}

if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'setConfig'){
	// TODO save
	echo 'saving this data is not yet implemented :(<br>';
}
echo 'checking for php >= 5.3.2... '.check(strnatcmp(phpversion(),'5.3.2') >= 0);
echo 'checking for soap... '.check(extension_loaded('soap'));
echo 'checking for json... '.check(extension_loaded('json'));
echo 'checking for rrd... '.check(extension_loaded('rrd'));
echo 'checking for reflection... '.check(extension_loaded('Reflection'));
echo 'checking for config... '.check($config = ConfigQuery::create()->findOne() != null);
echo 'checking for post_max_size >= 4M... '.check(ini_get('post_max_size')> '4M');
echo '<br><br>';
echo 'configuration:'.new KBr();
$form = new KForm(array(new KAttribute('method', 'post'), new KAttribute('action', 'setup.php?do=setConfig')));
$table = new KTable(array(new KAttribute('border', '1')));
$table->addThRow(array('key', 'value'));
$table->addRow(array('url', new KInputfield(array(new KAttributeName('url'), new KAttributeId('url'), new KAttributeValue(YaffmapConfig::get('url')), new Kattribute('size', '50')))));
$table->addRow(array('adminUser', new KInputfield(array(new KAttributeName('adminUser'), new KAttributeId('adminUser'), new KAttributeValue(YaffmapConfig::get('adminUser')), new Kattribute('size', '50')))));
$table->addRow(array('adminPasswd', new KInputfield(array(new KAttributeName('adminPasswd'), new KAttributeId('adminPasswd'), new KAttributeValue(YaffmapConfig::get('adminPasswd')), new Kattribute('size', '50')))));
$table->addRow(array('defaultTree', new KInputfield(array(new KAttributeName('defaultTree'), new KAttributeId('defaultTree'), new KAttributeValue(YaffmapConfig::get('defaultTree')), new Kattribute('size', '50')))));
$table->addRow(array('deleteOldNode', new KInputfield(array(new KAttributeName('deleteOldNode'), new KAttributeId('deleteOldNode'), new KAttributeValue(YaffmapConfig::get('deleteOldNode')), new Kattribute('size', '48'))).'h'));
$table->addRow(array('deleteOldLink', new KInputfield(array(new KAttributeName('deleteOldLink'), new KAttributeId('deleteOldLink'), new KAttributeValue(YaffmapConfig::get('deleteOldLink')), new Kattribute('size', '48'))).'h'));
$table->addRow(array('deleteOldBackend', new KInputfield(array(new KAttributeName('deleteOldBackend'), new KAttributeId('deleteOldBackend'), new KAttributeValue(YaffmapConfig::get('deleteOldBackend')), new Kattribute('size', '48'))).'h'));
$tr = $table->addRow(array(new KTd(array(new KAttribute('colspan', '2')), array(new KButton(array(new KAttributeValue('save')))))));
$form->addItem($table);
echo $form;
?>