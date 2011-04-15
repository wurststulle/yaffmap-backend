<html xmlns="http://www.w3.org/1999/xhtml"> 
  <head> 
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
echo new KScript('openlayers/OpenLayers.js');
echo new KScript('js/yaffmap.js');
?>

  <link rel="stylesheet" href="openlayers/theme/default/style.css" type="text/css">
  <link rel="stylesheet" href="openlayers/theme/default/google.css" type="text/css">
  <link rel="stylesheet" href="openlayers/style1.css" type="text/css">
  <link rel="stylesheet" href="yaffmap.css" type="text/css">
<script type="text/javascript" src="js/soapclient24.js"></script>
  <script type="text/javascript">
$(document).ready(function(){
	getYaffMap<?php echo (($_SERVER['REMOTE_ADDR'] == '192.168.2.21')?"":"Ext"); ?>('yaffmap');
});
function showPane()
{
  document.getElementById('yaffmapPane').style.visibility='visible';
}

function hidePane()
{
  document.getElementById('yaffmapPane').style.visibility='hidden'; 
}
function setPaneContent(title, content) {
  showPane();
  document.getElementById('yaffmapPaneTitle').innerHTML = title;
  document.getElementById('yaffmapPaneContent').innerHTML = content; 
}

  </script>
  <title> Yet Another FreiFunk Map</title>
</head>
<body>
  <div id="yaffmapPane" class="yaffmapPane">
    <div id="yaffmapPaneTitle" class="yaffmapPaneTitle"> </div>
    <div id="yaffmapPaneTabbar" class="yaffmapPaneTabbar"> </div> 
    <div class="yaffmapPaneClose" onClick="hidePane();";> </div> 
    <div id="yaffmapPaneContent" class="yaffmapPaneContent"> </div>
  </div>
  <div id="yaffmap"></div>

</body>
</html>
