<?php
/**
 * Proton lisääminen kantaan
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * */
require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/classes/protot.php");

$paluu = isset($_SESSION["paluu"]) ? $_SESSION["paluu"] : false;

function tv($nimi, $def=false, &$ra=false) {
    $arvo = isset($_POST[$nimi]) ? $_POST[$nimi] : $def;
    if($ra!==false)
        $ra[$nimi]=$arvo;
    return $arvo;
}

$ra=array();
$metodi = tv("metodi", false, $ra);
$nimi= tv("nimi", false, $ra);
$omistaja = tv("omistaja", false, $ra);
$suunnittelijat=tv("suunnittelijat", false, $ra);
$kesto=tv("kesto", false, $ra);
$minp = tv("minp", false, $ra);
$maxp = tv("maxp", false, $ra);
$saannot = tv("saannot", false, $ra);
$kohdeyleiso= tv("kohdeyleiso", false, $ra);
$versio = tv("versio", false, $ra);
$status = tv("status", false, $ra);
$kuvaus = tv("kuvaus", false, $ra);
$luoja = $_SESSION["user"]["tunniste"];
$ra["luoja"]=$luoja;
$ra["omistaja_ktunnus"]=$ra["omistaja"];
$sijainti = tv("sijainti", fasle, $ra);
$db = new SLSDB();
$users = new SLSUSERS($db);

$onko = $users->fetchWithTunnus($omistaja);
if($onko===false) {
    $_SESSION["p_virhe"]=sprintf(_("Tuntematon omistaja : %s"),htmlentities($omistaja));
    foreach($ra as $k=>$v) {
        $_SESSION["p_".$k]=$v;        
    }
    header("Location: {$_SESSION["paluu"]}");
    die();
}

$protot = new PROTOT($db);
$onko = $protot->findWithRex($nimi, "nimi");
if($onko===false && $metodi=="lisää") {
    $id=$protot->addProto($ra);
} else {
    if($metodi=="lisää") {
        $_SESSION["p_virhe"]=sprintf(_("Proto %s on jo kannassa."), htmlentities($nimi));
        foreach($ra as $k=>$v) {
            $_SESSION["p_".$k]=$v;        
        }
        header("Location: {$_SESSION["paluu"]}");
        die();
    }
    die("$metodi ei vielä ole tuettu!");
}
?>