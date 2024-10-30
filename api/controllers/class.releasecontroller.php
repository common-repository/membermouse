<?php

class ReleaseController implements RestController {
    function execute(RestServer $rest) {}
    
    /*
     * Requires version # and minor_version
     */
    public function deployMMVersion($rest){
    	$post = $rest->getRequest()->getPost();
        Utils::logRequest(json_encode($post), "/deployMMVersion");
    
        if(!isset($post["version"])){
        	return new Response($rest,  "You must provide a version number",RESPONSE_ERROR_MESSAGE_MISSING_PARAMS." : version",RESPONSE_ERROR_CODE_MISSING_PARAMS, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        }
        
        $minorVersion = (isset($post["minor_version"]))?$post["minor_version"]:"";
        $version= MM_Site::getPluginVersion();
        
        if($version != $post["version"]){
        	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_MAJOR_VERSION_NOTICE,$post["version"]);
        	return new Response($rest,  "Major versions do not match but user has been notified of new release","Major versions do not match",RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        }
        
		$pluginName = array_pop(explode(DIRECTORY_SEPARATOR, dirname(dirname(dirname(__FILE__)))));
        $writeableDir = ABSPATH."wp-content/plugins/".$pluginName."/com/membermouse/cache";
        if(is_dir($writeableDir)){
        	if(is_writeable($writeableDir)){
	        	if ($handle = opendir($writeableDir)) {
				    while (false !== ($file = readdir($handle))) {
				        if(!is_dir($file)){
				        	@unlink($writeableDir."/".$file);
				        }
				    }
				    closedir($handle);
				}
        	}	
        }
        
		$ret = MM_MemberMouseService::getRelease($version, $minorVersion);
		if($ret instanceof MM_Response){
			return new Response($rest, "Could not find major-minor pair ({$version}.{$minorVersion})",$ret->message,RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
		}
        if($ret!==false){
        	MM_OptionUtils::setOption(MM_OptionUtils::$OPTION_KEY_MINOR_VERSION,$minorVersion);
        }
        
        if(defined("DB_NAME")){
        	global $wpdb;
        	
        	if(file_exists($writeableDir."/membermouse_schema.sql")){
        		
				$phpObj = new MM_PhpObj($wpdb, DB_NAME);
				if(!$phpObj->importFile($writeableDir."/membermouse_schema.sql", true)){
		        	return new Response($rest,  "Could not update MM DB","Could not update MM DB",RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
				}
				$versionRelease = new MM_VersionRelease();
				$versionRelease->setVersion($post["version"].".".$minorVersion);
				$versionRelease->getDataByVersion();
				
				$notes = $versionRelease->getNotes();
				if(isset($post["notes"])){
					$notes.="\n";
					$notes.=$post["notes"];
					$versionRelease->setNotes($notes);
				}
				$versionRelease->commitData();
				
				return new Response($rest);
        	}
        	else{
		        return new Response($rest,  "Could not find ".$writeableDir."/membermouse_schema.sql","Could not find ".$writeableDir."/membermouse_schema.sql",RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
        	}
        }
   		else{
   			return new Response($rest,  "DB_NAME not defined","DB_NAME not defined",RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
   		}
		        
        
        return new Response($rest,  "Could not update MM class version","Could not update MM class version",RESPONSE_ERROR_CODE_BAD_REQUEST, RESPONSE_ERROR_MESSAGE_MISSING_PARAMS);
    } 
}