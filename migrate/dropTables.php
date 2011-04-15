<?php
require_once '../build/propel/Propel.php';
Propel::init('../build/conf/yaffmap-conf.php');
set_include_path("../build/classes".PATH_SEPARATOR.get_include_path());

$con = Propel::getConnection(ConfigPeer::DATABASE_NAME);
$sql = "DROP TABLE `yaffmap_v_addrMapNode`, `yaffmap_v_rpLinkLocation`, `yaffmap_v_unlocatedNodes`";
$stmt = $con->prepare($sql);
$stmt->execute();
?>