<?php

//Change as needed
$base_path = $_SERVER['DOCUMENT_ROOT'];
//This GZIPs the CSS for transmission to the user
//making file size smaller and transfer rate quicker
ob_start("ob_gzhandler");

//Function for compressing the CSS as tightly as possible
function compress($buffer) {
    //Remove CSS comments
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    //Remove tabs, spaces, newlines, etc.
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    $buffer = preg_replace('`\s+`', ' ', $buffer);
    return $buffer;
}

//Creative rewriting
$pi = $_SERVER['PATH_INFO'];
$pia = explode('/', $pi);

$pia_len = count($pia);
$i = 1;

while($i < $pia_len)
{
	$j = $i+1;
	$j = (isset($pia[$j])) ? $j : $i;
	
	$_GET[$pia[$i]] = $pia[$j];
	
	$i = $j + 1;
};

//Include the css groups
$groups = require("./config/css_groups.php");

$css = '';
$modified = array();

if(isset($groups[$_GET['g']]))
{
	foreach($groups[$_GET['g']] as $file)
	{
		$new_file = realpath($base_path.$file);
		$css .= file_get_contents($new_file);
		$modified[] = filemtime($new_file);
	}
}

//Add this page too
$modified[] = filemtime($base_path."css.php");

//Get the latest modified date
rsort($modified);
$last_modified = $modified[0];

if(!isset($_GET['debug']))
{
	$css = compress($css);
}

$requested_time=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) 
	? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) 
	: time();

if($last_modified === $requested_time)
{
	header("HTTP/1.1 304 Not Modified");
	exit();
}

header("Content-Type: text/css; charset=utf8");
header("Cache-control: public, max-age=691200, must-revalidate");
header("Last-Modified: ".gmdate('D, d M Y H:i:s', $last_modified)." GMT");
header("Expires: ".gmdate('D, d M Y H:i:s', (filemtime($base_path.'css.php') + 691200))." GMT");

echo $css;

ob_end_flush();
//End of css.php
