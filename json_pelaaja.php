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

$result=array("virhe"=>false, "virheet"=>"", "data"=>"");

if(!isset($_SESSION["s_sessioid"]) || $_SESSION["s_sessioid"]=="") {
    $result["virhe"]=true;
    $result["virheet"]=_("Ei istuntoa!<br/>");
    $result["data"]=$_REQUEST;
}

if(!isset($_POST["numero"]) || $_POST["numero"]=="") {
   $result["virhe"]=true;
   $result["virheet"]=_("Ei pelaajanumeroa!<br/>");
   $result["data"]=$_REQUEST; 
}

if($result["virhe"]==false) { 
    $db = new SLSDB();
    $p = new PELAAJA($db);
    $t = $p->haePelaaja($_SESSION["s_sessioid"], $_POST["numero"]);
    if($t===false) {
        $result["virhe"]=true;
        $result["virheet"]=sprintf(_("Pelaajahaku numerolla %d mätti!"),$_POST["numero"]);
        $result["data"]=$_REQUEST;
    }
    else {
        $result["data"]=$t;
    }
}
header("Content-type: application/json");
echo json_encode($result);
?>