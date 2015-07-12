<?php
/**
 * Globaalit parametrit
 *
 * Tietokanta-asetukset, istuntoasetukset
 * @package SLS-Proto-tracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * */

ini_set("variables_order","EGPCS");
$development=true;

if($development==true) {
    $dbuser='mos';
    $dbpassword='foobar';
    $dbport="5432";
    $dbhost="10.1.0.110";
    $baseurl="http://localhost/~mos/proto";
}

$dbname="proto";
$dsn="pgsql:host=$dbhost;port=$dbport;dbname=$dbname";
$basepath=__DIR__;

/** @var SESSION_TIMEOUT int Istunnon kesto sekunneissa */
define("SESSION_TIMEOUT", 6*60*60);
/** @var SESSION_NAME string Istunnon nimi */
define("SESSION_NAME", "prototracker");
/** @var SESSION_COOKIEPATH string Evästeen polku */
define("SESSION_COOKIEPATH", "/");
/** @var REDIRECT_URI string Googlen autentikaation urli */
define("REDIRECT_URI", "$baseurl/google_login.php");

define("URL_MUNGLER", false);
ini_set('session.referer_check', "");
?>
