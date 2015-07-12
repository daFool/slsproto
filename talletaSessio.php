<?php
require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/helpers/minrights.php");
require_once("$basepath/classes/sessiot.php");
require_once("$basepath/classes/protot.php");

if(!isset($_SESSION["s_sessioid"]) || $_SESSION["s_sessioid"]=="") {
    $metodi="lisää";
    $sessioid="";    
}
else {
    $metodi="talleta";
    $sessioid=$_SESSION["s_sessioid"];
}
$result=true;
$d=array();
$f=array("ajankohta"=>false, "proto"=>false, "versio"=>false, "saannotluettu"=>"ei", "saantoselitys_alkoi"=>"", "saantoselitys_loppui"=>"",
         "setup_alkoi"=>"","setup_loppui"=>"", "peli_alkoi"=>"", "peli_loppui", "vuoron_kesto"=>"", "kierroksen_kesto"=>"", "kierroksia"=>"",
         "lopputoimet_alkoivat"=>"", "lopputoimet_loppuivat"=>"", "kuvaus"=>"");
$e=false;

foreach($f as $k=>$v) {
    if(!isset($_POST[$k]) && $v==false) {
        if($e===false)
            $e = _("Pakollisen kentän $k arvo puuttui!<br/>");
        else
            $e .= _("Pakollisen kentän $k arvo puuttui!<br/>");
        continue;
    }
    if(isset($_POST[$k]) && $_POST[$k]!="") {
        $d[$k]=$_POST[$k];
    }
}

$d["luoja"]=$_SESSION['user']['tunniste'];

if($e!==false) {
    $result=array("virhe"=>true, "virheet"=>$e, "data"=>$_REQUEST);
}
else {
    $sessio = new SESSIOT($db);
    $protot = new PROTOT($db);
    
    $poro = $protot->findWithRex($d["proto"], "nimi");
    if($poro === false) {
        $result=array("virhe"=>true, "virheet"=>sprintf(_("Protoa %s ei löytynyt"),$d['proto']), "data"=>$_REQUEST);
    }
    else {
        $d["proto"]=$poro[0]["id"];
        if($metodi=="lisää")
            $res=$sessio->addSession($d);
        else
            $res=$sessio->saveSession($sessioid, $d);
        if($res===false) {
            $result=array("virhe"=>true, "virheet"=>_("Session talletus epäonnistui."), "data"=>$_REQUEST);
        } else {
            $_SESSION["s_sessioid"]=$res;
            $_SESSION["s_protoid"]=$d["proto"];
            $data=array("sessioid"=>$res);
            $result=array("virhe"=>false, "data"=>$data);
        }
    }
}
header("Content-type: application/json");
echo json_encode($result);
?>