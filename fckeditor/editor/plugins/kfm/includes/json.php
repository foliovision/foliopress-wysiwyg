<?php
function kfm_json_encode($obj){
	$json=new kfm_Services_JSON();
	return $json->encode($obj);
}
function kfm_json_decode($js){
	$json=new kfm_Services_JSON();
	return $json->decode($js);
}
