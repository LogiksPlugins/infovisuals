<?php
if(!defined('ROOT')) exit('No direct script access allowed');

if(!function_exists("findInfovisual")) {

	function findInfovisual($file) {
		$fileName=$file;
		if(!file_exists($file)) {
			$file=str_replace(".","/",$file);
		}

		$fsArr=[
				$file,
				APPROOT.APPS_MISC_FOLDER."infovisuals/{$file}.json",
			];
		if(isset($_REQUEST['forSite']) && defined("CMS_SITENAME")) {
			$fsArr[]=ROOT."apps/".CMS_SITENAME."/".APPS_MISC_FOLDER."infovisuals/{$file}.json";
		}
		
		$fArr = explode("/",$file);
		if(count($fArr)>1) {
			$fPath = checkModule($fArr[0]);
			if($fPath) {
				unset($fArr[0]);
				$fsArr[] = dirname($fPath)."/infovisuals/".implode("/",$fArr).".json";
			}
		}

		$file=false;
		foreach ($fsArr as $fs) {
			if(file_exists($fs)) {
				$file=$fs;
				break;
			}
		}
		if(!file_exists($file)) {
			return false;
		}

		$reportData=file_get_contents($file);
		$reportData=_replace($reportData);
		$infovisualConfig=json_decode($reportData,true);

		if(count($infovisualConfig)<1) {
			return false;
		}

		$infovisualConfig['sourcefile']=$file;
		$infovisualConfig['reportkey']=md5(session_id().time().$file);
		$infovisualConfig['reportgkey']=md5($file);
		$infovisualConfig['srckey']=$fileName;
		if(!isset($infovisualConfig['dbkey'])) $infovisualConfig['dbkey']="app";

		return $infovisualConfig;
	}

	function printInfovisual($infovisualConfig,$dbKey="app",$params=[]) {
// 		var_dump($infovisualConfig);exit();
		if(!is_array($infovisualConfig)) $infovisualConfig=findReport($infovisualConfig);

		if(!is_array($infovisualConfig) || count($infovisualConfig)<=2) {
			trigger_logikserror("Corrupt infovisual defination");
			return false;
		}


	}

	function printCard($cardInfo,$dbKey="app",$params=[]) {
		
	}
}

if(!function_exists("searchMedia")) {
	function searchMedia($media) {
		if(strpos($media,"https://")===0 || strpos($media,"http://")===0) {
			$ext=explode(".",current(explode("?",$media)));
			$ext=strtolower(end($ext));

			return [
				"name"=>basename($media),
				"raw"=>$media,
				"src"=>$media,
				"url"=>$media,
				"size"=>0,
				"ext"=>$ext,
			];
		}
		if(isset($_REQUEST['forsite'])) {
			$fs=_fs($_REQUEST['forsite'],[
					"driver"=>"local",
					"basedir"=>ROOT.APPS_FOLDER.$_REQUEST['forsite']."/".APPS_USERDATA_FOLDER
				]);
		} else {
			$fs=_fs();
			$fs->cd(APPS_USERDATA_FOLDER);
		}
		$mediaDir=$fs->pwd();

		if(file_exists($media)) {
			$ext=explode(".",$media);
			$mediaName=explode("_",basename($media));
			$mediaName=array_slice($mediaName,1);
			$mediaName=implode("_",$mediaName);
			return [
				"name"=>$mediaName,
				"raw"=>$media,
				"src"=>$media,
				"url"=>getWebPath($media),
				"size"=>filesize($media)/1024,
				"ext"=>strtolower(end($ext)),
			];
		} elseif(file_exists($mediaDir.$media)) {
			$ext=explode(".",$media);
			$mediaName=explode("_",basename($media));
			$mediaName=array_slice($mediaName,1);
			$mediaName=implode("_",$mediaName);
			return [
				"name"=>$mediaName,
				"raw"=>$media,
				"src"=>$mediaDir.$media,
				"url"=>getWebPath($mediaDir.$media),
				"size"=>filesize($mediaDir.$media)/1024,
				"ext"=>strtolower(end($ext)),
			];
		} else {
			return false;
		}
	}
}
?>