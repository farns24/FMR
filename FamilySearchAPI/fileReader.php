<?php 
$fileName = $_GET["fileName"];
//echo $fileName;
if ($fileName!='')
{
	$fileContents = file_get_contents("../data/v2/$fileName");
	$xml = simplexml_load_string($fileContents);

	$json = json_encode($xml);
	echo $json;
}
else
{
	echo "";
}
?>