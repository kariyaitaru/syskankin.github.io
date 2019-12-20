<?php
//ＤＢ接続情報を取得するクラス
class DbInfo{
	var $exists;
	var $uid;
	var $pwd;
	var $dsn;
	var $pdd;

	function DbInfo($filepath){
		$this->exists=TRUE;
		$this->uid='';
		$this->pwd='';
		$this->dsn='';
		$this->pdd='30';
		if(file_exists($filepath)){
			$fl=fopen($filepath,'r');
			while(!feof($fl)){
				$line=str_replace(' ','',fgets($fl,128));
				$keyw=substr($line,0,4);
				switch($keyw){
					case 'UID=':
						$this->uid=str_replace($keyw,'',$line);
						break;
					case 'PWD=':
						$this->pwd=str_replace($keyw,'',$line);
						break;
					case 'DSN=':
						$this->dsn=str_replace($keyw,'',$line);
						break;
					case 'PDD=':
						$this->dsn=str_replace($keyw,'',$line);
						break;
					default:
						break;
				}
			}
			fclose($fl);
		}else{
			$this->exists=FALSE;
		}
	}

	function getUid(){
		return $this->uid;
	}

	function getPwd(){
		return $this->pwd;
	}

	function getDsn(){
		return $this->dsn;
	}
	function getPdd(){
		return $this->pdd;
	}

	function checkFile(){
		return $this->exists;
	}
}
