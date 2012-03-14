<?php
require_once('initialise.php');
$sn=$_POST['name'];
$help=file_get_contents('http://kfmdoc.companytools.nl/kfm_setting_help.php?name='.$sn);
echo $help;
?>
