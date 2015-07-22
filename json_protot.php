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

$a = array("id", "nimi", "omistaja", "luotu", "muokattu", "status" );
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
$protot=$p->tableFetch($start, $length, $od, $search, $_SESSION["user"]["tunniste"]);
$jason = array("draw"=>$draw, "recordsTotal"=>$protot["lkm"], "recordsFiltered"=>$protot["filtered"]);
$data = array();
$i=0;
foreach($protot["protot"] as $rivi) {
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
