<?php
ini_set("soap.wsdl_cache_enabled", "0");

require_once 'propel/Propel.php';
Propel::init("conf/yaffmap-conf.php");
set_include_path("classes" . PATH_SEPARATOR . get_include_path());
include dirname(__FILE__).'/classes/Autoloader.php';

$config = Config::getConfig();

$server = new YaffmapSoapServer();

$classmap = SoapClassMap::getMap();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$server->handle();
}else{
	switch($_SERVER['QUERY_STRING']){
	case 'wsdl':
		header('Content-Type: application/xml; charset=UTF-8');
		$s = new YaffmapWsdlCreator($classmap);
		echo $s->createWsdl($config->getUrl());
		break;
	case 'schema':
		header('Content-Type: application/xml; charset=UTF-8');
		$s = new YaffmapWsdlCreator($classmap);
		echo $s->createWsdlSchema($config->getUrl());
		break;
	case 'test':
		echo Kobold::dump($_SERVER);
		break;
	default:
		$client = new SoapClient($config->getUrl().'/soap.php?wsdl', array('location' => $config->getUrl().'/soap.php', 'classmap' => $classmap));
		echo Kobold::dump('====================== METHODS =============================<br>');
		echo Kobold::dump($client->__getFunctions());
		echo Kobold::dump('====================== TYPES =============================<br>');
		echo Kobold::dump($client->__getTypes());
	}
}
?>