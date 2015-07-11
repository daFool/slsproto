<?php
require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/helpers/minrights.php");
require_once("$basepath/classes/sessiot.php");
require_once("$basepath/classes/protot.php");
require_once("$basepath/classes/pelaaja.php");

$result=array("virhe"=>false, "virheet"=>"", "data"=>"");

if(!isset($_SESSION["s_sessioid"]) || $_SESSION["s_sessioid"]=="") {
    $result["virhe"]=true;
    $result["virheet"]=_("Ei istuntoa!<br/>");
    $result["data"]=$_REQUEST;
}

if(!isset($_POST["pe_jrnro"]) || $_POST["pe_jrnro"]=="") {
    $result["virhe"]=true;
    $result["virheet"].=_("Ei järjestysnumeroa!<br/>");
    $result["data"]=$_REQUEST;
}
if($result["virhe"]==false) {
    $f = array("pe_tunnus"=>"tunnus", "pe_sijoitus"=>"sijoitus", "pe_tulos"=>"tulos", "pe_nimi"=>"nimi",
               "pe_aiemmin"=>"aiemmin", "pe_ostaisitko"=>"ostaisitko", "pe_pelaisitko"=>"pelaisitko", "pe_sosiaalisuus"=>"pe_sosiaalisuus",
               "pe_tuuri"=>"tuuri", "pe_taktiikka"=>"taktiikka", "pe_fiilis"=>"fiilis", "pe_uutuus"=>"uutuus", "pe_mekaniikka"=>"mekaniikka",
               "pe_idea"=>"idea", "pe_kelle"=>"kelle", "pe_assosiaatiot"=>"assosiaatiot", "pe_kokemus"=>"kokemus", "pe_aikuiset"=>"aikuiset",
               "pe_kasuaalit"=>"kasuaalit", "pe_lapset"=>"lapset", "pe_perheet"=>"perheet", "pe_pelaajat"=>"pelaajat",
               "pe_nuoret"=>"nuoret");
    $d=array();
    foreach($f as $k=>$v) {
        if(!isset($_POST[$k]))
            continue;
        switch($k) {
            case "pe_aiemmin":
            case "pe_ostaisitko":
            case "pe_pelaisitko":
                if($_POST[$k]=="ei")
                    $d[$v]=false;
                else
                    $d[$v]=true;
                break;
            case "pe_perheet":
            case "pe_lapset":
            case "pe_kasuaalit":
            case "pe_nuoret":
            case "pe_aikuiset":
            case "pe_pelaajat":
               if(isset($d["kelle"]))
                $d["kelle"].=" $v";
               else
                $d["kelle"]="$v"; 
                break;
            default:
                if($_POST[$k]!="")
                    $d[$v]=$_POST[$k];
                break;
        }
    }
    $p = new PELAAJA($db);
    if($p->talletaPelaaja($_SESSION["s_sessioid"], $_POST["pe_jrnro"], $d)) {
        $result["data"]=$_POST;
    } else {
        $result["virhe"]=true;
        $result["virheet"]=_("Talletus epäonnistui.<br>");
    }
}
header("Content-type: application/json");
echo json_encode($result);
?>