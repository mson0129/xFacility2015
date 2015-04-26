<?php
//xFacility2015
//Loader
//Studio2b
//Michael Son(mson0129@gmail.com)
//22SEP2014(1.0.0.) - Loader is updated.
//16MAR2015(1.1.0.) - Loader is divided from XFObject.class.php of xFacility2014. And path setting is modified. It's set automatically.
//27APR2015(1.1.1.) - Stopping with a non-exist path is corrected.

function load($classPath=NULL) {
	if(!is_null($classPath)) {
		if(substr($classPath, -1)=="/")
			$classPath = substr($classPath, 0, -1);
		$paths[0] = $_SERVER['DOCUMENT_ROOT'].$classPath;
	} else {
		$paths[0] = substr(__FILE__, 0, strlen(__FILE__)-strlen("/loader.php"))."/classes";
	}
	$j = 1;
	for($i=0; $i<$j; $i++) {
		$handle = opendir($paths[$i]);
		if(is_dir($paths[$i])) {
			while(false !==($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if(!is_dir($paths[$i]."/".$file)) {
						if(substr($file, 0, 1)!=".") {
							if($_GET['debug']==true)
								echo $paths[$i]."/".$file;
							require_once($paths[$i]."/".$file);
							if($_GET['debug']==true)
								echo "...OK\n";
						}
					} else {
						$paths[] = $paths[$i]."/".$file;
						$j = count($paths);
					}
				}
			}
		}
	}
	return false;
}

load();
?>