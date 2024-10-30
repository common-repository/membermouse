<?php
/**
 * 
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */
define("MM_USE_STREAM_WRAPPERS", "0");
if(!defined("BASE_DIR")){
	define("BASE_DIR", dirname(dirname(__FILE__))."/api/");
}

function useStreamWrappers(){
	return MM_USE_STREAM_WRAPPERS==="1";
}

function loadFileContents($path){
	if(file_exists($path)){
		$contents = file_get_contents($path);
		if($contents !== false){
			return base64_decode(stripslashes($contents));
		}
	}
	return false;
}

function showLoadedClasses($class, $str=""){
	if(isset($_GET["debug"])){
		if($_GET["debug"]=="log"){
			LogMe::write($class." ".$str);
		}
		else{
			echo $class." ".$str."<br />";
		}
	}
}

function __autoload($class_name) 
{
	$class_file_name = preg_replace("/(MM_)/","",$class_name);
	
	$foundClass = false;

	if(defined("ABSPATH"))
	{	
		if(!$foundClass){
			if(preg_match("/(hooks)/", $class_name))
			{
				$dir = "hooks";
				$file = ABSPATH."/wp-content/plugins/".MM_PLUGIN_NAME."/".$dir."/class.".strtolower($class_file_name).".php";
				
				if(file_exists($file))
				{
					$foundClass = true;
					require_once($file);
				}
			}
			else
			{
				$dirs = array("lib", "com/membermouse/util",  "managers", 'hooks');
				
				foreach($dirs as $dir)
				{
					$file = ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/".$dir."/class.".strtolower($class_file_name).".php";
					if(file_exists($file))
					{		
						$foundClass = true;
						require_once($file);
					}
				}
			}
		}
	}
	
	if(!isLocalInstall("membermouse.localhost") && !$foundClass){
        if(class_exists($class_name,false)){
        	$foundClass = true;	
        }
        else{
			if(!preg_match("/(membermouseservice)/", strtolower($class_name))){
	        	$writeableDir = ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/com/membermouse/cache";
				if(is_writeable($writeableDir)){
					if(is_dir($writeableDir)){
						$tmpClassName = strtolower($class_file_name);
						$classObj = loadFileContents($writeableDir."/".base64_encode($tmpClassName).".cache");
						if(useStreamWrappers()){
							$streamObj = "<?php ".$classObj;
							if (!in_array('membermouseStream', stream_get_wrappers())) {
								stream_wrapper_register("membermouseStream", "MM_MemberMouseStream");
					        }
					        if($classObj !== false && !empty($classObj)){
								include('membermouseStream://streamObj');
					        }
						}
						$foundClass = false;	
			        	if(!class_exists($class_name,false)){
			        		$classObj = preg_replace("/^(<\?php)/", "", $classObj);
							if(@eval($classObj) !==false){
					        	if(!class_exists($class_name,false)){
									$classObj = loadFileContents($writeableDir."/".base64_encode($tmpClassName).".cache_1");
									if($classObj!==false && @eval($classObj) !==false){
					        			if(class_exists($class_name,false)){
					        				showLoadedClasses($class_name, "Eval Added {$class_name} from cache");
											$foundClass = true;	
					        			}
									}
					        	}
					        	else{
					        		showLoadedClasses($class_name, "Eval Added {$class_name} from cache");
					        		$foundClass = true;
					        	}
						    }
			        	}
			        	else{
					        showLoadedClasses($class_name, "<b>Stream</b> Added {$class_name} from cache");
			        		$foundClass = true;
			        	}
				    }
				}
			}
			if(!$foundClass)
			{
				global $wpdb;
				$sql = "select obj from ".MM_TABLE_CONTAINER." where LOWER(name)='%s'";
				$row = $wpdb->get_row($wpdb->prepare($sql, strtolower($class_file_name)));
				if(isset($row->obj) && !empty($row->obj))
				{
					$classObj = base64_decode(stripslashes($row->obj));
					if(useStreamWrappers()){
						if (!in_array('membermouseStream', stream_get_wrappers())) {
							stream_wrapper_register("membermouseStream", "MM_MemberMouseStream");
			        	}
						include('membermouseStream://classObj');
					}
					if(!class_exists($class_name, false)){
						$classObj = preg_replace("/^(<\?php)/", "", $classObj);
						if(@eval($classObj)!==false){
							if(class_exists($class_name, false)){
			        			$foundClass = true;
				       			showLoadedClasses($class_name, "<b>DB Class</b> Added {$class_name}");
							}
						}
					}
				}
			}
        }
	}
	
	if(defined("ABSPATH"))
	{	
		if(!$foundClass){
			if(preg_match("/(hooks)/", $class_name))
			{
				$dir = "hooks";
				$file = ABSPATH."/wp-content/plugins/".MM_PLUGIN_NAME."/".$dir."/class.".strtolower($class_file_name).".php";
				
				if(file_exists($file))
				{
					$foundClass = true;
					require_once($file);
				}
			}
			else
			{
				$dirs = array();
				$dirs[] = "com/membermouse/service";
				$dirs[] = "com/membermouse/engine";
				$dirs[] = "com/membermouse/entity";
				$dirs[] = "com/membermouse/import";
				$dirs[] = "com/membermouse/view";
				$dirs[] = "com/membermouse/engine";
				
				foreach($dirs as $dir)
				{
					$file = ABSPATH."wp-content/plugins/".MM_PLUGIN_NAME."/".$dir."/class.".strtolower($class_file_name).".php";
					
					if(file_exists($file))
					{		
						$foundClass = true;
						require_once($file);
					}
				}
			}
		}
	}
}
?>
