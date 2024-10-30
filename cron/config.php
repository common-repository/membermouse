<?php
echo "Starting config.php\n";
if(!isset($doNotLoadMM)){
	require_once("../../../../wp-load.php");
	require_once("../includes/mm-constants.php");
	require_once("../includes/init.php");
}
echo "Loaded wp, constants, and init.\n";
/*
 * Lets auto load all libraries
 */
$baseDir = dirname(__FILE__);
if ($handle = opendir($baseDir."/lib")) {
    while (false !== ($file = readdir($handle))) 
    {
    	if($file != "." && $file != ".." && preg_match("/(\.php)$/", $file))
    	{
    		$filename= $baseDir."/lib/".$file;
    		echo "--Loading {$filename}\n";
	    	require_once($filename);
    	}   
    }
    closedir($handle);
}

echo "Loaded lib files.\n";

$dirnameArr = array('modules');
foreach($dirnameArr as $dirname){
	$filepath = trim($baseDir."/".$dirname);
	if ($handle = opendir($filepath)) {
	    while (false !== ($file = readdir($handle))) 
	    {
	    	if(preg_match("/(\.php)$/", $file))
	    	{
	    		echo "adding {$filepath}/{$file}\n";
		    	require_once($filepath."/".$file);
	    	}   
	    }
	    closedir($handle);
	}
}

?>
