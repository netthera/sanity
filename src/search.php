<?php

chdir($_SERVER['DOCUMENT_ROOT']);
$dir = '.';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$stack=iterateDirectory($iterator);
$stack = array_unique($stack);
sort($stack);

foreach($stack as $key => $dir){
	$buffer='';
	$buffer=globdir($dir);
	if ($buffer !=''){
		echo '<a href=/sanity/index.php?p=/' .$dir. '>' .$dir. "</a><br>\n";
		echo $buffer;
	}
}

function globdir($dir){
	chdir($_SERVER['DOCUMENT_ROOT'].$dir);
	$buffer='';
	$pattern = '/'.$_GET['pattern'].'/';
 	$pat =$_GET['pattern'];
	foreach (glob("{*.php,*.js}",GLOB_BRACE ) as $filename) {
    //echo "$filename size " . filesize($filename) . "\n";
    $input = file_get_contents($filename);
    //
    $string = grep($pattern, $input, $context = 1);
   
    $output = htmlspecialchars($string);
		if ($output != ''){
			//$buffer.= "$filename";
		$buffer.= '<a href=/sanity/index.php?p=' .$dir.'&view='.$filename.'>' .$filename. "</a><br>\n";

			$buffer.= '<pre>';
  		$buffer.= $output = str_replace($pat, '<b>'.$pat.'</b>', $output);
    	$buffer.= '</pre>';
  	}
	}
	return $buffer;
}

function iterateDirectory($i){
		$stack = array();
    foreach ($i as $path) {
        if ($path->isDir()){
            $clean_path = str_replace('.','', $path);
            array_push($stack,$clean_path);
            iterateDirectory($path);
        }
    }
    return $stack;
}

?>
<?php
/**
 * grep.php
 *
 * Search a block of text for a given text string
 * 
 * @param   string        $pattern
 *  A regexp to match against
 * @param   string|array  $input
 *  A string or array (can't handle recursiveness) to search
 * @param   int           $context
 *  Returns x number of lines surrounding the hit
 * @return  string        $output
 * Written by: Simon Ljungberg
 * https://gist.github.com/simme/407402
 */
function grep($pattern, $input, $context = 0) {
  if (is_string($input)) {
    $input = explode("\n", $input);
  }
  if (!is_array($input)) {
    throw new InvalidArgumentException('Invalid input must be string or array.');
  }
  $output = array();
  foreach ($input as $line => $string) {
    if (preg_match($pattern, $string)) {
    	//echo htmlspecialchars($string);
      if ($context > 0) {
        $start = ($line - $context < 0) ? 0 : $line - $context;
        $end   = ($line + $context > count($input)) ? count($input) - 1 : $line + $context;
        $o = '';
        for ($start; $start <= $end; $start++) {
          $o .= $start + 1 . ': ' . $input[$start] . "\n";
        }
        $output[] = "\n" . $o;
      }
      else {
        $output[] = $line + 1 . ': ' . $string;
      }
    }
  }
  $output =  (empty($output)) ? FALSE : join($output, "\n\n----------------------\n\n");
  
  return $output;
}
