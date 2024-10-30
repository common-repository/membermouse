<?php
function loadFile($file){
		if(file_exists($file)){
			return file_get_contents($file);
		}
		return "";
}

function getJsFiles($directory, $recursive = false, $includeDirs = false, $pattern = '/.*/'){
	$items = array();
	
	if($handle = opendir($directory)) {
		while (($file = readdir($handle)) !== false) {
			if ($file != '.' && $file != '..') {
				$path = "$directory/$file";
				$path = preg_replace('#//#si', '/', $path);
				if (is_dir($path)) {
					if ($includeDirs) {
						$items[] = $path;
					}
					if ($recursive) {
						$items = array_merge($items, self::getFilesFromDir($path, true, $includeDirs, $pattern));
					}
				}
				else {
					if (preg_match($pattern, $file)) {
						$items[] = $path;
					}
				}
			}
		}
		
		closedir($handle);
	}
	sort($items);
	
	return $items;
}

$baseDir = preg_replace("/(\/)$/", "", dirname(__FILE__));
$userDir = getJsFiles($baseDir."/user", false, false);
$adminDir = getJsFiles($baseDir."/admin", false, false);

$js = "";
foreach($userDir as $file){
	$js.= "\n/** {$file} **/\n";
	
	if(!file_exists($file)){
		continue;
	}
	$js.= loadFile($file);
	$js.= "\n/** End of {$file} **/\n";
}
foreach($adminDir as $file){
	$js.= "\n/** {$file} **/\n";
	
	if(!file_exists($file)){
		continue;
	}
	$js.= loadFile($file);
	$js.= "\n/** End of {$file} **/\n";
}

echo $js;