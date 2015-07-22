<?php
/**
 * Väli php session tietojen lataamiseen
 * 
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * @uses globals.php
 * @uses database.php
 * @uses protot.php
 * */

require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/classes/sessiot.php");
require_once("$basepath/classes/protot.php");


require_once("$basepath/helpers/minrights.php");

$sessioid=isset($_REQUEST["sessioid"]) ? $_REQUEST["sessioid"] : false;
if($sessioid===false) {
    header("Location: $baseurl/index.php");
    die();
}

$sessiot = new SESSIOT($db);
$res = $sessiot->lataaSessio($sessioid, $_SESSION["user"]["tunniste"]);
if($res===false) {
    header("Location: $baseurl/index.php");
    die();
}
$protot = new PROTOT($db);
$proto = $protot->haeProto($res['proto']);

foreach($res as $k=>$v) {
    switch($k) {
        case 'proto':
            $_SESSION["s_".$k]=$proto["nimi"];
            break;
        case 'ajankohta':
        case 'saantoselitys_alkoi':
        case 'saantoselitys_loppui':
        case 'setup_alkoi':
        case 'setup_loppui':
        case 'peli_alkoi':
        case 'peli_loppui':
        case 'lopputoimet_alkoivat':
        case 'lopputoimet_loppuivat':
            @list($p, $t)=explode(' ',$v);
            $_SESSION["s_".$k]=$p."T".substr($t,0,5);
            break;
        default:
            $_SESSION["s_".$k]=$v;
    }    
}
$_SESSION["s_sessioid"]=$sessioid;
$_SESSION["s_metodi"]="muuta";

header("Location: $baseurl/forms/sessio_main.php");
die();
?>
