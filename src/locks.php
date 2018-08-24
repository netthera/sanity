<?php

$locks_ra = array();
snt_load_locks();

function snt_lock_file($path){
	global $file_path;
	$myFile = "locks.json";
  $arr_data = array();
	$snt_user=$_SERVER['REMOTE_USER'];
	$snt_error="";
  try{
		$jsondata = file_get_contents($myFile);
		$arr_data = json_decode($jsondata, true);
		if($arr_data[$path]=='')
	   	$arr_data[$path] = $snt_user;
		else
			$snt_error='Error: File locked by: '.$arr_data[$path];
			
		if($snt_error==""){	
	   	$jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
	  	if(file_put_contents($myFile, $jsondata)) {
				$snt_error= 'File successfully locked';
	      make_path('revs/'.$path);
	      $rev = get_rev_num($path);
	      if (!copy($_SERVER['DOCUMENT_ROOT'].'/'.$path, 'revs/'.$path.$rev)) {
    			echo "failed to copy $path...\n";
    			$errors= error_get_last();
    			echo "COPY ERROR: ".$errors['type'];
    			echo "<br />\n".$errors['message'];
				}
	    }else 
	        $snt_error="Error: Couldn't lock ".$path;  
	  }
			echo $snt_error;
  }catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
	}	
}

function snt_unlock_file($path){
	$myFile = "locks.json";
  $arr_data = array(); // create empty array
	$snt_user=$_SERVER['REMOTE_USER'];
	$snt_error="";

	try{
	  $jsondata = file_get_contents($myFile);
	  $arr_data = json_decode($jsondata, true);
	  if($arr_data[$path]!=''){
			if($arr_data[$path]== $snt_user)
	   		unset ($arr_data[$path]);
			else
				$snt_error='Error: File can only be unlocked by: '.$arr_data[$path];
	  }else
			$snt_error='Error: File is not locked';
			
		if($snt_error==""){	
	   	$jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);
	   // Write data into locks.json file
	  	if(file_put_contents($myFile, $jsondata)) 
	        $snt_error= 'File successfully unlocked';
	    else 
	        $snt_error="Error: Couldn't unlock ".$path;
	        
	  }
		echo $snt_error;
	}catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
  }
			
}

function snt_load_locks(){
	global $locks_ra;
	$myFile = "locks.json";
	$jsondata = file_get_contents($myFile);
	$locks_ra = json_decode($jsondata, true);
}

function snt_islocked($path){
	global $locks_ra;
	return $locks_ra[$path];
}


function make_path($path){
	$dir = pathinfo($path , PATHINFO_DIRNAME);
	if( is_dir($dir) ){
		return true;
	}else{
		if( make_path($dir) ){
			if( mkdir($dir) ){
				chmod($dir , 0775);
				return true;
			}
    }
  }
  return false;
}

function get_rev_num($path){
	$i=1;
	$filename= 'revs/'.$path.'.rev'.$i;
	while(file_exists ('revs/'.$path.'.rev'.$i)){
		$i++;
	}
	return '.rev'.$i;
}
function get_cur_rev($path){
	$i=1;
	$filename= 'revs/'.$path.'.rev'.$i;
	while(file_exists ('revs/'.$path.'.rev'.$i)){
		$i++;
	}
	$i--;
	if ($i==0)
		return '';
	else
		return 'sanity/revs/'.$path.'.rev'.$i;
}

?>
