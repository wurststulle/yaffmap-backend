<?php
class YaffmapNewAgentRelease extends Yaffmap{
	
	public function __construct($request = null, $response = null){
		call_user_func_array('parent::__construct', array($request, $response));
		$allowed = array('tree', 'release', 'version', 'isHead', 'uploadedFile');
		$this->checkInput($allowed);
	}
	
	public function newAgentReleaseWithFile(){
		$hasError = true;
		$tmp = explode("-", $this->request['release']);
		$relName = $tmp[0];
		$relSubName = $tmp[1];
		if(!isset($this->request['tree']) || !isset($this->request['release']) || !isset($this->request['version'])){
			throw new EIsufficientQuery('tree, release or version missing.');
		}
		if(isset($this->request['isHead']) && $this->request['isHead'] == 'true'){
			$oldHead = UpgradeQuery::create()
				->filterByIsHead(true)
				->filterByUpgradeTree($this->request['tree'])
				->filterByVersion($this->request['version'])
				->findOne();
			$release = UpgradeQuery::create()
				->filterByUpgradeTree($this->request['tree'])
				->filterByRelease($relName)
				->filterBySubRelease($relSubName)
				->filterByVersion($this->request['version'])
				->findOneOrCreate();
			if(!$release->isNew()){
				throw new YaffmapLoggedException('Release already exists.');
			}
			$release->setIsHead(true);
		}else{
			// release will not be head release
			$release = UpgradeQuery::create()
				->filterByUpgradeTree($this->request['tree'])
				->filterByRelease($relName)
				->filterBySubRelease($relSubName)
				->filterByVersion($this->request['version'])
				->findOneOrCreate();
			if(!$release->isNew()){
				throw new YaffmapLoggedException('Release already exists.');
			}
		}
		$release->setUrl('http://wurststulle.dyndns.org/yaffmap/download/'.$relName.'/'.$this->assembleFileName());
		$hasError = false;
		if(!$hasError){
			if(!is_writable(dirname(__FILE__).'/../')){
				throw new YaffmapException('base dir not writeable.');
			}
			// upload file
			$CHUNK = 8192;
			if(!($putData = fopen("php://input", "r"))){
				throw new YaffmapLoggedException("Can't get PUT data."); 
			}
			$tot_write = 0;
			@mkdir('download/'.$relName, 0644, true);
			@mkdir('download/.backup/'.$relName, 0644, true);
            $destFile = 'download/'.$relName.'/'.$this->assembleFileName(); 
            if(!is_file($destFile)){
                fclose(fopen($destFile, "x"));
                if(!($fp = fopen($destFile, "w"))){
                	throw new YaffmapLoggedException("Can't write to file");
                }
                while($data = fread($putData, $CHUNK)){
                    $chunk_read = strlen($data);
                    if(($block_write = fwrite($fp, $data)) != $chunk_read){
                    	throw new YaffmapLoggedException("Can't write more to file");
                    }
                    $tot_write += $block_write;
                }
                if(!fclose($fp)){
                	throw new YaffmapLoggedException("Can't close file");
                }
                fclose($putData);
                unset($putData);
            }
            if($tot_write >= 20480){
            	@unlink('download/'.$relName.'/'.$this->assembleFileName());
            	throw new YaffmapLoggedException("File is bigger than 20480 byte.");
            }
			$this->response->setErrorCode(ResponseCodeNode::OPERATION_SUCCEDED);
			$this->response->setErrorMsg('Operation Succeded.');
		}
		if(!$hasError){
			if($oldHead != null){
				// old head release existing, delete head flag
				$oldHead->setIsHead(false);
				$oldHead->save();
				@unlink('download/yaffmap_latest_'.$this->request['version'].'_'.$this->request['tree'].'.tar.gz');
				symlink(''.$relName.'/'.$this->assembleFileName(), 'download/yaffmap_latest_'.$this->request['version'].'_'.$this->request['tree'].'.tar.gz');
				copy('download/'.$relName.'/'.$this->assembleFileName(),
					'download/.backup/'.$relName.'/'.$this->assembleFileName());
			}
			$release->setReleaseDate(new DateTime("now"));
			$release->save();
			$this->write_sql_defaults();
		}
		return $this->response;
	}
	
	private function assembleFileName(){
		$tmp = explode("-", $this->request['release']);
		$relName = $tmp[0];
		$relSubName = $tmp[1];
		return 'yaffmap_'.$relName.'-'.$relSubName.'_'.$this->request['version'].'_'.$this->request['tree'].'.tar.gz';
	}
	
	public static function write_sql_defaults(){
		if(!($fh = fopen('sql/defaults.upgrade.sql', 'w'))){
			throw new YaffmapLoggedException("Can't create sql/defaults.upgrade.sql."); 
		}
		fwrite($fh, "INSERT INTO `yaffmap_upgrade` (`release`, `subRelease`, `tree`, `url`, `version`, `isHead`, `releaseDate`) VALUES \n");
		$upgrades = UpgradeQuery::create()->find();
		$num = $upgrades->count();
		$i = 0;
		foreach($upgrades as $upgrade){
			if($num-1 == $i){
				fwrite($fh, "('".$upgrade->getRelease()."', '".$upgrade->getSubRelease()."', '".$upgrade->getUpgradeTree()."', '".$upgrade->getUrl()."', '".$upgrade->getVersion()."', ".(($upgrade->getIsHead() == true)?'1':'0').", '".$upgrade->getReleaseDate()."');");
			}else{
				fwrite($fh, "('".$upgrade->getRelease()."', '".$upgrade->getSubRelease()."','".$upgrade->getUpgradeTree()."', '".$upgrade->getUrl()."', '".$upgrade->getVersion()."', ".(($upgrade->getIsHead() == true)?'1':'0').", '".$upgrade->getReleaseDate()."'),\n");
			}
			$i++;
		}
		fclose($fh);
	}
}
?>