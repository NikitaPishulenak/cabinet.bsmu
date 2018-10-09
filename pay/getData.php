<?php

function curl_download($Url){
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    //curl_setopt($ch, CURLOPT_REFERER, "http://cabinet.bsmu.by/pay/view.php");
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    curl_setopt($ch, CURLOPT_CRLF, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    $output = curl_exec($ch);
    curl_close($ch);
 
    return $output;
}


$uri = "http://Admin:2022323@172.20.0.136:22231/ElectronicJournalHTTP/hs/ElectronicJournal/EJ/?";

$uri = (empty($_GET['idStudent'])) ? $uri : $uri.'idStudent='.$_GET['idStudent'];
$uri = (empty($_GET['status'])) ? $uri : $uri.'&status='.$_GET['status'];
$uri = (empty($_GET['p'])) ? $uri : $uri.'&p='.$_GET['p'];
$uri = (empty($_GET['l'])) ? $uri : $uri.'&l='.$_GET['l'];
$uri = (empty($_GET['ex'])) ? $uri : $uri.'&ex='.$_GET['ex'];
$uri = (empty($_GET['nameSubject'])) ? $uri : $uri.'&nameSubject='.str_replace(' ', '_',$_GET['nameSubject']);
//$uri = (empty($_GET['datLes'])) ? $uri : $uri.'&datLes='.$_GET['datLes'];
$uri = (empty($_GET['idOrder'])) ? $uri : $uri.'&idOrder='.$_GET['idOrder'];
//echo "<pre> $uri".PHP_EOL;
echo curl_download($uri);