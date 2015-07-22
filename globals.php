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
$basepath=__DIR__;
require_once("$basepath/private.php");

$development=true;

if($development==true) {
    $dbport="5432";
    $dbhost="127.0.0.1";
    $baseurl="http://slsproto.tuolla.com";
}

$dbname="slsproto";
$dsn="pgsql:host=$dbhost;port=$dbport;dbname=$dbname";

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

$email_from="Lautapelikirjasto <mos@iki.fi>";
$email_host="error.claymountain.com";

$google_appname="SLS Proto";

?>
