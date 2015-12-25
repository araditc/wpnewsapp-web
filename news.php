<?php

include 'config.php';
include 'Funcsapi.php';
$mdl = new Funcsapi();

$token = $_REQUEST["my_app"];
$jsonData = $mdl->getPosts($params,$token);

//var_export($jsonData);
//$jsonData = get_object_vars($jsonData);
//var_dump($jsonData);
?>
<?php 
if(!empty($jsonData)):
?>
{
<?php 
if(!empty($jsonData["ok"])):
?>
"ok":"true",
<?php 
else:
?>
"ok":"false"
<?php 
endif;
?>
<?php 
	if(!empty($jsonData["items"])):
?>
"result":
[
<?php 
		$lastid = count($jsonData["items"])-1;
		foreach ($jsonData["items"] as $k=>$item):
			if($k == $lastid):
?>
		{"id":"<?php echo $item["ID"];?>","datetime":"<?php echo $item["post_date"];?>","title":"<?php echo $item["post_title"];?>","content":"<?php echo $item["post_content"];?>","image":"<?php echo $item["post_image"];?>","cat":"<?php echo $item["post_category"];?>","link":"<?php echo $item["post_link"];?>"}
<?php 
			else:
?>
		{"id":"<?php echo $item["ID"];?>","datetime":"<?php echo $item["post_date"];?>","title":"<?php echo $item["post_title"];?>","content":"<?php echo $item["post_content"];?>","image":"<?php echo $item["post_image"];?>","cat":"<?php echo $item["post_category"];?>","link":"<?php echo $item["post_link"];?>"},
<?php 
			endif;			
		endforeach;
?>
]
<?php 
	endif;
?>
}
<?php 
endif;
?>

