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
		$infovisualConfig['infovisualkey']=md5(session_id().time().$file);
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

		if($params==null) $params=[];
		$infovisualConfig=array_replace_recursive($infovisualConfig,$params);
		
		if(isset($_SESSION['INFOVISUAL_CONFIG']) && is_array($_SESSION['INFOVISUAL_CONFIG'])) {
			$globalParams = $_SESSION['INFOVISUAL_CONFIG'];
			$infovisualConfig=array_replace_recursive($infovisualConfig,$globalParams);
		}

		if(!isset($infovisualConfig['infovisualkey'])) $infovisualConfig['infovisualkey']=md5(session_id().time());

		$infovisualConfig['dbkey']=$dbKey;

		if(!isset($infovisualConfig['template']) || strlen($infovisualConfig['template'])<=0) {
			$infovisualConfig['template']="layout1";
		}
		//setCookie('INFOVISUAL-'.$infovisualConfig['reportgkey'],$infovisualConfig['template'],0,"/");
		setCookie('INFOVISUAL-'.$infovisualConfig['reportgkey'],$infovisualConfig['template'],0,"/",$_SERVER['SERVER_NAME'], isHTTPS());

		if(!isset($infovisualConfig['params']) || count($infovisualConfig['params'])<=0) {
			$infovisualConfig['params'] = [];
		}

		if(!isset($infovisualConfig['cards']) || count($infovisualConfig['cards'])<=0) {
			echo "<h2 class='text-center'>No cards found for the infovisual</h2>";
			return;
		}

		foreach ($infovisualConfig['cards'] as $key => $cardValue) {
			unset($infovisualConfig['cards'][$key]);

			$infovisualConfig['cards'][md5(session_id().time().$key)] = $cardValue;
		}

		$infovisualkey=$infovisualConfig['infovisualkey'];
		$_SESSION['INFOVISUAL'][$infovisualkey]=$infovisualConfig;

		$templateArr=[
				$infovisualConfig['template'],
				__DIR__."/templates/{$infovisualConfig['template']}.php"
			];
		foreach ($templateArr as $f) {
			if(file_exists($f) && is_file($f)) {

				if(isset($infovisualConfig['preload'])) {
					if(isset($infovisualConfig['preload']['modules'])) {
						loadModules($infovisualConfig['preload']['modules']);
					}
					if(isset($infovisualConfig['preload']['api'])) {
						foreach ($infovisualConfig['preload']['api'] as $apiModule) {
							loadModuleLib($apiModule,'api');
						}
					}
					if(isset($infovisualConfig['preload']['helpers'])) {
						loadHelpers($infovisualConfig['preload']['helpers']);
					}
					if(isset($infovisualConfig['preload']['method'])) {
						if(!is_array($infovisualConfig['preload']['method'])) $infovisualConfig['preload']['method']=explode(",",$infovisualConfig['preload']['method']);
						foreach($infovisualConfig['preload']['method'] as $m) call_user_func($m,$infovisualConfig);
					}
					if(isset($infovisualConfig['preload']['file'])) {
						if(!is_array($infovisualConfig['preload']['file'])) $infovisualConfig['preload']['file']=explode(",",$infovisualConfig['preload']['file']);
						foreach($infovisualConfig['preload']['file'] as $m) {
							if(file_exists($m)) include $m;
							elseif(file_exists(APPROOT.$m)) include APPROOT.$m;
						}
					}
				}
				
				$preoadJS = ['jquery.cookie', 'moment','infovisuals'];//"filesaver","html2canvas"
				$preoadCSS = ['infovisuals'];

				if(isset($infovisualConfig['cards'])) {
					foreach ($infovisualConfig['cards'] as $cardKey => $cardConfig) {
						if(isset($cardConfig['js'])) {
							if(is_string($cardConfig['js'])) $cardConfig['js'] = explode(",", $cardConfig['js']);
							$preoadJS = array_merge($preoadJS, $cardConfig['js']);
						}
						if(isset($cardConfig['css'])) {
							if(is_string($cardConfig['css'])) $cardConfig['css'] = explode(",", $cardConfig['css']);
							$preoadCSS = array_merge($preoadCSS, $cardConfig['css']);
						}
					}
				}

				if(isset($infovisualConfig['style']) && strlen($infovisualConfig['style'])>0) {
					$preoadCSS = array_merge($preoadCSS, ["reports/{$infovisualConfig['style']}",$infovisualConfig['style']]);
				}
				if(isset($infovisualConfig['script']) && strlen($infovisualConfig['script'])>0) {
					$preoadJS = array_merge($preoadJS, ["reports/{$infovisualConfig['script']}",$infovisualConfig['script']]);
				}

				echo _css(array_unique($preoadCSS));

				echo "<div class='infovisuals-container clearfix nopadding'>";

				include_once $f;

				echo "</div>";

				echo _js(array_unique($preoadJS));
				

				return true;
			}
		}

		trigger_logikserror("Infovisual Template Not Found",null,404);
	}

	function printCardBox($infovisualkey, $cardKey, $cardConfig, $infovisualsParams = []) {
		$infovisualsParams = array_merge([
				"SHOW_HEADERS"=>true,
				"SHOW_FOOTERS"=>true,
			],$infovisualsParams);

		if(!isset($cardConfig['column'])) $cardConfig['column'] = 4;
		if(!isset($cardConfig['column_large'])) $cardConfig['column_large'] = $cardConfig['column'];
		if(!isset($cardConfig['column_small'])) $cardConfig['column_small'] = $cardConfig['column'];

		$cardConfig = array_merge([
				"column"=>6,
				"column_large"=>6,
				"column_small"=>6,

				"forcenewrow"=>false,
				"title"=>"",
				"header"=>false,
				"footer"=>false,
				"active"=>true,
				"newrow"=>false,

				"containerClass"=>"",
			],$cardConfig);

		// printArray($cardConfig);
		if($cardConfig['newrow']) {
			$cardConfig['containerClass'] = trim($cardConfig['containerClass'])." newRowLeft";
		}

		$html = "<div id='ivk{$cardKey}' class='infovisualBox col-xs-12 col-sm-{$cardConfig['column_small']} col-md-{$cardConfig['column']} col-lg-{$cardConfig['column_large']} {$cardConfig['containerClass']}'  data-ivcardkey='{$cardKey}' data-ivkey='{$infovisualkey}'>";

		$html .= "<div class='panel panel-default infovisualCard'>";

		if($infovisualsParams['SHOW_HEADERS']) {
			if($cardConfig['header']) {
				if(file_exists($cardConfig['header'])) {
					include_once $cardConfig['header'];
				} elseif(file_exists(__DIR__."/comps/{$cardConfig['header']}.php")) {
					include_once __DIR__."/comps/{$cardConfig['header']}.php";
				} elseif(!is_numeric($cardConfig['header']) && strlen($cardConfig['header'])>0) {
					$html .= "<div class='panel-heading'>";

					if(isset($cardConfig['actions']) && is_array($cardConfig['actions']) && count($cardConfig['actions'])>0) {

					}

					$html .= $cardConfig['header'];
					$html .= "</div>";	
				}
			} elseif(strlen($cardConfig['title'])>0) {
				$html .= "<div class='panel-heading'>";

				if(isset($cardConfig['actions']) && is_array($cardConfig['actions']) && count($cardConfig['actions'])>0) {
					$html .= "<div class='pull-right card-toolbar'>";
					$html .= createInfovisualCardButtons($cardConfig);
					$html .= "</div>";
				}

				$html .= "<h3 class='panel-title'>{$cardConfig['title']}</h3>";
				$html .= "</div>";
			}
		}

		$html .= "<div class='panel-body'>";
		$html .= "</div>";

		if($infovisualsParams['SHOW_FOOTERS']) {
			if($cardConfig['footer']) {
				if(file_exists($cardConfig['footer'])) {
					include_once $cardConfig['footer'];
				} elseif(file_exists(__DIR__."/comps/{$cardConfig['footer']}.php")) {
					include_once __DIR__."/comps/{$cardConfig['footer']}.php";
				} elseif(!is_numeric($cardConfig['footer']) && strlen($cardConfig['footer'])>0) {
					$html .= "<div class='panel-footer'>{$cardConfig['footer']}</div>";
				}
			}
		}

		$html .= "</div>";

		$html .= "</div>";
		return $html;
	}

	function printInfovisualCard($cardKey, $cardConfig, $dbKey="app",$params=[]) {
		$colorArr = [
			"red" => 'rgb(255, 99, 132)',
			"orange" => 'rgb(255, 159, 64)',
			"yellow" => 'rgb(255, 205, 86)',
			"green" => 'rgb(75, 192, 192)',
			"blue" => 'rgb(54, 162, 235)',
			"purple" => 'rgb(153, 102, 255)',
			"grey" => 'rgb(201, 203, 207)',

			"clr_1" => '#4dc9f6',
			"pink" => '#f67019',
			"darkblue" => '#f53794',
			"lightgreen" => '#537bc4',
			"darkblue" => '#acc236',
			"clr_6" => '#166a8f',
			"clr_7" => '#00a950',
			"clr_8" => '#58595b',
			"clr_9" => '#8549ba'
		];
		$colorKeys = array_keys($colorArr);

		if(!isset($cardConfig['height'])) $cardConfig['height'] = "300px";

		if(isset($cardConfig['type'])) {
			$cardFile = __DIR__."/cards/{$cardConfig['type']}.php";
			if(file_exists($cardConfig['type'])) {
				include_once $cardConfig['type'];
			} elseif(file_exists($cardFile)) {
				include_once $cardFile;
			} else {
				echo "<p>Source Card not found</p>";
			}

		}
	}

	function fetchCardData($cardConfig, $dbKey="app") {
		$cardData = false;

		if(isset($cardConfig['source']) && is_array($cardConfig['source']) && count($cardConfig['source'])>0) {
			foreach ($cardConfig['source'] as $kn => $source) {
				$cardDBKey = $dbKey;
				if(isset($source['dbkey'])) $cardDBKey = $source['dbkey'];

				$dbData = _db($cardDBKey)->queryBuilder()->fromJSON(json_encode($source),_db($cardDBKey));
				if($dbData) {
					$tempData = $dbData->_GET();

					if($tempData) {
						if(!$cardData) $cardData = [];
						$cardData[$kn] = $tempData;
					}
				}
			}
		}

		return $cardData;
	}

	function createInfovisualCardButtons($cardConfig) {
		$html = "";
		foreach ($cardConfig['actions'] as $key => $button) {
          if(isset($button['policy']) && strlen($button['policy'])>0) {
            $allow=checkUserPolicy($button['policy']);
            if(!$allow) continue;
          }
//           printArray($button);printArray();checkUserRoles($cardConfig['srckey'],);
          if(isset($button['label'])) $button['label']=_ling($button['label']);
          else $button['label']=_ling($key);
          
          if(isset($button['title'])) $button['title']=_ling($button['title']);
          else $button['title']="";

          if(strlen($button['title'])<=0) $button['title'] = $button['label'];

          if(!isset($button['class'])) $button['class']="";
          $html .="<a class='actionCMD btn btn-cardtool {$button['class']}' data-cmd='{$key}' title='{$button['title']}' >";
          if(isset($button['icon']) && strlen($button['icon'])>0) {
            if(strpos($button['icon'],"<")!==false) {
              $html .=$button['icon'];
            } else {
              $html .="<i class='{$button['icon']}'></i> ";
            }
          }
          // $html .=" <span class='btn-label'>{$button['label']}</span>";
          $html .="</a>";
        }
        return $html;
	}

	function createInfovisualRecordAction($button, $record) {
		if(isset($button['policy']) && strlen($button['policy'])>0) {
			$allow=checkUserPolicy($button['policy']);
			if(!$allow) return "";
		}
		
		$cmd=$button['cmd'];
		
		if(!isset($button['icon'])) return;
		if(!isset($button['label'])) $button['label']="";
		if(!isset($button['class'])) $button['class']="";
		
		$button['label']=_ling($button['label']);
		
		$_ENV['REPORTRECORD']=$record;
		$_ENV['REPORTRECORD']['hashid']="";
		
		if(!empty($record)) {
			if(isset($record['hashid'])) $_ENV['REPORTRECORD']['hashid']=$record['hashid'];
			elseif(isset($record['id'])) $_ENV['REPORTRECORD']['hashid']=md5($record['id']);
			else {
				$_ENV['REPORTRECORD']['hashid']=md5($record[array_keys($record)[0]]);
			}
		}
		$cmd=preg_replace_callback('/{(.*?)}/', function($matches) {
			$colName=substr($matches[0],1,strlen($matches[0])-2);
			if(isset($_ENV['REPORTRECORD'][$colName])) {
				if(is_numeric($_ENV['REPORTRECORD'][$colName])) return md5($_ENV['REPORTRECORD'][$colName]);
				return $_ENV['REPORTRECORD'][$colName];
			}
			return "";
		}, $cmd);
		if(isset($_ENV['REPORTRECORD'])) unset($_ENV['REPORTRECORD']);

		return "<i class='actionCMD {$button['icon']} {$button['class']}' data-cmd='{$cmd}' title='{$button['label']}'></i>";
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