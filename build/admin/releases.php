<?php
require_once '../propel/Propel.php';
Propel::init("../conf/yaffmap-conf.php");
set_include_path("../classes" . PATH_SEPARATOR . get_include_path());
require_once '../classes/Yaffmap.php';
require_once '../classes/kobold/Kobold.php';
require_once '../classes/PHPLiveX/PHPLiveX.php';
require_once 'YaffmapAdmin.php';

echo new KScript('js/jquery.min.js');

class YDeleteImageButton extends KImageButton{
	
	public function __construct($text, $onclick, $disabled = false){
		$this->addAttribute(array('id' => 'deleteButton'));
		$this->addAttribute(array('src' => 'images/delete.png'));
		$this->addAttribute(array('alt' => $text));
		$this->addListener(new KListenerOnclick(new KAction($onclick)));
		if($disabled){
			$this->disabled();
			$this->addAttribute(array('src' => 'images/delete_disabled.png'));
		}
		call_user_func('parent::__construct');
	}
}

$admin = new YaffmapAdmin();

$ajax = new PHPLiveX();
$ajax->AjaxifyObjects(array('admin'));
$ajax->Run("../classes/PHPLiveX/phplivex.js");

$trees = UpgradeQuery::create()->groupByUpgradeTree()->find();
echo $releases;

$table = new KTable();
$table->addAttribute(array(new KAttribute('border', 1)));
$table->addThRow(array('Release', 'Version', 'Date', new KBlanc()));

foreach($trees as $tree){
	$td = new KTd();
	$td->addItem('UpgradeTree: '.$tree->getUpgradeTree());
	$td->addAttribute(array(new KAttributeColspan('4')));
	$table->addThRow(array($td));
	$releases = UpgradeQuery::create()->filterByUpgradeTree($tree->getUpgradeTree())
	->filterByIsHead(true)
	->orderByVersion()
	->orderByRelease('desc')->orderBySubRelease('desc')->find();
	foreach($releases as $release){
		$table->addRow(array($release->getRelease().'-'.$release->getSubRelease(), $release->getVersion(), $release->getReleaseDate(), new KSimpleLink($release->getUrl(), 'download')));
	}
}
echo $table;
?>
<script type="text/javascript">
function deleteRelease(id, tree, version, item){
	if(confirm('delete?')){
		admin.deleteRelease(id, tree, version, {url: 'releases.php', onFinish: function(response){
				if(getResponse(response) == 1){
					item.parent().parent().hide();
				}
			}
		});
	}
}
</script>
