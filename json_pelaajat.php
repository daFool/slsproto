<?php
/**
 * Javascript-palvelu pelaajien listaamiseen
 * 
 * @package SLS-Proto
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * @uses globals.php
 * @uses database.php
 * @uses games.php
 * */

require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/classes/pelaaja.php");

require_once("$basepath/helpers/minrights.php");
 
$db = new SLSDB();
 
$draw = isset($_REQUEST["draw"]) ? $_REQUEST["draw"] : false;
$start = isset($_REQUEST["start"]) ? $_REQUEST["start"] : 0;
$length = isset($_REQUEST["length"]) ? $_REQUEST["length"] : 10;
$search = isset($_REQUEST["search"]) ? $_REQUEST["search"] : false;
$order = isset($_REQUEST["order"]) ? $_REQUEST["order"] : false;
$columns = isset($_REQUEST["columns"]) ? $_REQUEST["columns"] : false;

if(!isset($_SESSION["s_sessioid"])) {
    $jason = array("draw"=>$draw, "recordsTotal"=>0, "recordsFiltered"=>0, "data"=>"");
    header("Content-type: application/json");
    echo json_encode($jason);
    die();
}

$sessioid=$_SESSION["s_sessioid"];

$a = ["numero", "nimi"];
$p = new PELAAJA($db);
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
$pelaajat=$p->tableFetch($start, $length, $od, $search, $sessioid);
$jason = array("draw"=>$draw, "recordsTotal"=>$pelaajat["lkm"], "recordsFiltered"=>$pelaajat["filtered"]);
$data = array();
$i=0;
foreach($pelaajat["pelaajat"] as $rivi) {
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