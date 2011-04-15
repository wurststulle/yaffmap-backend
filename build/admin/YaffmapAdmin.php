<?php 
class YaffmapAdmin{
	
	public function __construct(){
		
	}
	
	public function deleteRelease($release, $tree, $version){
		$hasError = false;
		$response = new KoboldResponseJson();
		$release = UpgradeQuery::create()
			->filterByRelease($release)
			->filterByUpgradeTree($tree)
			->filterByVersion($version)
			->findOne();
		if($release == null){
			$response->setReturnValue(0);
		}else{
			$relName = explode("-", $release);
			try{
				unlink('../download/'.$relName[0].'/yaffmap_'.$release.'_'.$version.'_'.$tree.'.tar.gz');
			}catch(Exception $e){
				$response->addError(new KoboldResponseError('generic', 'can not delete file.'));
				$response->setReturnValue(0);
				$hasError = true;
			}
			if(!$hasError){
				$release->delete();
				if($release->getIsHead()){
					// add head attribute to one release before
					$old = UpgradeQuery::create()
						->filterByRelease($relName[0].($relName[1]-1))
						->filterByUpgradeTree($tree)
						->filterByVersion($version)
						->findOne();
					$old->setIsHead(true);
					$old->save();
				}
				$response->setReturnValue(1);
			}
		}
		return $response->get();
	}
}
?>