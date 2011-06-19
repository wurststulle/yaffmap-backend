<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';
require_once '../classes/PHPLiveX/PHPLiveX.php';

$ajax = new PHPLiveX();
$ajax->Run("../classes/PHPLiveX/phplivex.js");

echo KMenu::includeCss();
echo new KLink('style.css');
echo new KScript('js/jquery.min.js');
echo new KScript('js/scripts.js');
echo new KScript('js/dhtmlHistory.js');

/* map frontend includes  */
echo new KScript('openlayers/OpenLayers.js');
echo new KScript('js/yaffmap.js');

$div = new KDiv();
$div->addAttribute(array(new KAttributeId('i18nDiv')));
$div->addItem(array('<span id="preloadDiv" style="visibility:hidden;">'.new KImage("images/loading.gif").'</span>'));
echo $div;

$div = new KDiv();
$div->addAttribute(array(new KAttributeId('menuDiv')));
$menu = new KMenu();
$menu->addMenuTitle('Administration');
$menu->addMenuItem('javascript:load(\'nodes\', false);', 'Nodes with agent');
$menu->addMenuItem('javascript:load(\'mapping\', false);', 'mapping');
$menu->addMenuItem('javascript:load(\'unlocatedNodes\', false);', 'unlocatedNodes');
$menu->addMenuItem('javascript:load(\'changeLog\', false);', 'changeLog');
//if($_SERVER['REMOTE_ADDR'] == '192.168.2.21'){
//	$menu->addMenuItem('javascript:getYaffMap(\'content\');', 'Map');
//	$menu->addMenuItem('javascript:getYaffMapDebug(\'content\');', 'MapDebug');
//}else{
//	$menu->addMenuItem('javascript:getYaffMapExt(\'content\');', 'Map');
//}
$menu->addMenuTitle('Debug');
$menu->addMenuItem('javascript:load(\'errorLog\', false);', 'errorLog');
$menu->addMenuItem('javascript:getYaffMapDebug(\'content\');', 'MapDebug');
$menu->addMenuTitle('Karte');
$menu->addMenuItem('map.php', 'Map');
$div->addItem($menu);
echo $div;

$div = new KDiv();
$div->addAttribute(array(new KAttributeId('content')));
echo $div;
?>
