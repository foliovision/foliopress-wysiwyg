<?php
function kfm_lang($str,$v1='',$v2='',$v3=''){
	global $kfm_language,$kfm_langStrings;
	if(!isset($kfm_langStrings)){
		include KFM_BASE_PATH.'lang/'.$kfm_language.'.php';
		$GLOBAL['kfm_langStrings']=$kfm_langStrings;
	}
	if(isset($kfm_langStrings[$str]))$str=$kfm_langStrings[$str];
	$i=1;
	while(strpos($str,'%'.$i)!==false)$str=str_replace('%'.$i,${'v'.$i++},$str);
	return utf8_encode($str);
}
function kfm_translate($str,$context,$lang){
	// { retrieve from local database if it's there
	$_str=addslashes($str);
	$_context=addslashes($context);
	$_lang=addslashes($lang);
	$r=db_fetch_row("SELECT translation FROM ".KFM_DB_PREFIX."translations WHERE original='$_str' AND language='$_lang' AND context='$_context'");
	if(is_array($r) && count($r)){
		if($r['translation']!='')return $r['translation'];
		$GLOBALS['kfmdb']->query("DELETE FROM ".KFM_DB_PREFIX."translations WHERE original='$_str' AND language='$_lang' AND context='$_context'");
	}
	// }
	// { if not, retrieve from kfm.verens.com
	$trans=file_get_contents('http://kfm.verens.com/extras/translate.php?str='.urlencode($str).'&lang='.urlencode($lang).'&context='.urlencode($context));
	$_trans=addslashes($trans);
	$GLOBALS['kfmdb']->query("INSERT INTO ".KFM_DB_PREFIX."translations (original,translation,language,context,found) VALUES ('$_str','$_trans','$_lang','$_context',0)");
	return $trans;
	// }
}
