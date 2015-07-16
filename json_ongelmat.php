<?php
/**
 * Javascript-palvelu ongelmien listaamiseen
 * 
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * @uses globals.php
 * @uses database.php
 * @uses games.php
 * @uses ongelma.php
 * @uses minrights.php
 * */

require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/classes/ongelma.php");

require_once("$basepath/helpers/minrights.php");
 
$db = new SLSDB();
 
$draw = isset($_REQUEST["draw"]) ? $_REQUEST["draw"] : false;
$start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
$length = isset($_REQUEST["length"]) ? $_REQUEST["length"] : 10;
$search = isset($_REQUEST["search"]) ? $_REQUEST["search"] : false;
$order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : false;
$columns = isset($_REQUEST["columns"]) ? $_REQUEST["columns"] : false;

if(!isset($_SESSION["s_sessioid"]) || !isset($_SESSION["s_protoid"])) {
    $jason = array("draw"=>$draw, "recordsTotal"=>0, "recordsFiltered"=>0, "data"=>"");
    header("Content-type: application/json");
    echo json_encode($jason);
    die();
}

$sessioid=$_SESSION["s_sessioid"];
$protoid=$_SESSION["s_protoid"];

$a = ["id", "kuvaus", "laji"];
$ongelma = new ONGELMA($db);
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
$ongelmat=$ongelma->tableFetch($start, $length, $od, $search, $protoid);
$jason = array("draw"=>$draw, "recordsTotal"=>$ongelmat["lkm"], "recordsFiltered"=>$ongelmat["filtered"]);
$data = array();
$i=0;
foreach($ongelmat["ongelmat"] as $rivi) {
    $j=0;
    foreach($a as $k=>$v) {
        $data[$i][$j++]=$rivi[$v];
    }
    $i++;
}
$jason["data"]=$data;
header("Content-type: application/json");
echo json_encode($jason);
?>
