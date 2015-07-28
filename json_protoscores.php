<?php
/**
 * Javascript-palvelu protojen listaamiseen
 * 
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * @uses globals.php
 * @uses database.php
 * @uses protot.php
 * @uses minrights.php
 * */

require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/classes/protot.php");

require_once("$basepath/helpers/minrights.php");

$draw = isset($_REQUEST["draw"]) ? $_REQUEST["draw"] : false;
$start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
$length = isset($_REQUEST["length"]) ? $_REQUEST["length"] : 10;
$search = isset($_REQUEST["search"]) ? $_REQUEST["search"] : false;
$order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : false;
$columns = isset($_REQUEST["columns"]) ? $_REQUEST["columns"] : false;
$protoid=isset($_SESSION["protoid"]) ? $_SESSION["protoid"] : false;

$a = array("sessio", "numero", "score", "fiilis", "mekaniikka", "idea", "uutuus", "sosiaalisuus", "tuuri", "taktiikka", "strategia",
           "ostaisitko", "pelaisitko");
$p = new PROTOT($db);
$od=false;
if($order) {
    $od="";
    $first=true;
    foreach($order as $o) {
        if(isset($a[$o["column"]])) {
            $od.=$first ? "" : ", ";
            $od.=$a[$o["column"]]." ".$o["dir"];
            $first=false;
        }
    }
}
$protot=$p->tableFetch2($start, $length, $od, $search, $_SESSION["user"]["tunniste"], $protoid);
$jason = array("draw"=>$draw, "recordsTotal"=>$protot["lkm"], "recordsFiltered"=>$protot["filtered"]);
$data = array();
$i=0;
foreach($protot["protot"] as $rivi) {
    $j=0;
    foreach($a as $k) {
        switch($k) {
            case "ostaisitko":
            case "pelaisitko":
                if($rivi[$k]==true || $rivi[$k]=="true")
                    $va=_("Kyllä");
                else
                    $va=_("En");
                break;
            default:
                $va=$rivi[$k];
                break;
        }
        $data[$i][$j++]=$va;
    }
    $i++;
}
$jason["data"]=$data;
header("Content-type: application/json");
echo json_encode($jason);
?>
