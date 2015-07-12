<?php
require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/helpers/minrights.php");
require_once("$basepath/classes/sessiot.php");
require_once("$basepath/classes/protot.php");
require_once("$basepath/classes/ongelma.php");

$result=array("virhe"=>false, "virheet"=>"", "data"=>"");

if(!isset($_SESSION["s_sessioid"]) || $_SESSION["s_sessioid"]=="") {
    $result["virhe"]=true;
    $result["virheet"]=_("Ei istuntoa!<br/>");
    $result["data"]=$_REQUEST;
}

$p = new PROTOT($db);

if(!isset($_POST["on_proto"]) || $_POST["on_proto"]=="" || ($pid=$p->findWithRex($_POST["on_proto"], "nimi"))===false) {
    $result["virhe"]=true;
    $result["virheet"]=_("Ei prototunnistetta tai protoa ei löytynyt!<br/>");
    $result["data"]=$_REQUEST;
}

if($result["virhe"]==false) {    
    $f = array("on_kuvaus"=>"kuvaus", "on_laji"=>"laji");
    $d=array();
    foreach($f as $k=>$v) {
        if($_POST[$k]!="")
            $d[$v]=$_POST[$k];
    }
    $p = new ONGELMA($db);
    if(!isset($_POST["on_id"]))
        $id="";
    else
        $id=$_POST["on_id"];
        
    if($p->talletaOngelma($pid[0]['id'], $id, $d)) {
        $result["data"]=$_POST;
    } else {
        $result["virhe"]=true;
        $result["virheet"]=_("Talletus epäonnistui.<br>");
    }
}
header("Content-type: application/json");
echo json_encode($result);
?>
