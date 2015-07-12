<?php
/**
 * Etsii protoja, json-palvelu
 * 
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * @uses globals.php
 * @uses database.php
 * @uses users.php
 * */

require_once("globals.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/classes/protot.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/minrights.php");

$term = isset($_REQUEST["term"]) ? $_REQUEST["term"] : false;
$result = array(_("Ei hakutermiä"));

if($term !== false) {
    $protot = new PROTOT($db);
    $result = $protot->searchWithNamePart($term, $_SESSION['user']['tunniste']);
    if($result===false)
        $result = array();
    else {
        $r = array();
        foreach($result as $r1) {
            array_push($r, $r1["nimi"]);
            $result=$r;
        }
    }
}
header("Content-type: application/json");
echo json_encode($result);
?>