<?php
require_once 'emailer.php';

$emailer = rEmailer::load();

$theSettings->registerPlugin($plugin["name"],$pInfo["perms"]);
$jResult .= $emailer->get();

/*
$rat = rRatio::load();
if(!$rat->obtain())
	$jResult.="plugin.disable(); noty('ratio: '+theUILang.pluginCantStart,'error');";
else
	$theSettings->registerPlugin($plugin["name"],$pInfo["perms"]);
$jResult.=$rat->get();
*/