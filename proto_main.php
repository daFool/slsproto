<?php
/*
 * Proton esittely
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 * */
require_once("globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/database.php");
require_once("$basepath/classes/protot.php");
require_once("$basepath/helpers/minrights.php");

$protoid = isset($_REQUEST["protoid"]) ? $_REQUEST["protoid"] : false;

if($protoid == false) {
    header("Location: $baseurl/index.php");
    die();
}
$_SESSION["protoid"]=$protoid;

$proto = new PROTOT($db);
$tiedot = $proto->haeProto($protoid);
if($tiedot===false) {
    die("Ei proton haku mätti $protoid");
    header("Location: $baseurl/index.php");
    die();
}

require_once("$basepath/html_base.html");
?>
    <title><?php echo _("Proto");?></title>
    <script type="text/javascript">
        $(document).ready(function() {
            // Protot-taulu
            $('#scores').dataTable( {
                "processing" : true,
                "serverSide" : true,
                "responsive" : true,
                "orderMulti" : true,
                "search" : {
                    "regex" : true,
                    "casInsensitive" : true,
                    "smart" : true
                },
                "ajax" : "<?php echo "$baseurl/json_protoscores.php";?>",
                <?php include("$basepath/datatables_language.js");?>
            }
            );
            
            $("#scores tbody").on('click','tr', function () {
                console.log($(this).children("td:nth-child(1)").html());
                console.log($(this).children("td:nth-child(2)").html());
            });
        });        
    </script>
    </head>
    <body>
        <?php include_once("navbar.html");?>
        <section class="container">
            <div class="row">
                <section class="col-xs-12 col-sm-6 col-md-6">
                    <h2><?php echo _("Proto");?></h2>
                    <table class="table-striped table-bordered">
                        <caption><?php $tiedot["nimi"];?></caption>
                        <tbody>
                    <?php
                    $a = array("nimi"=>_("Nimi"), "omistaja"=>_("Omistaja"), "luotu"=>_("Luotu"),
                               "muokattu"=>_("Muokattu"),"kuvaus"=>_("Kuvaus"), "suunnittelijat"=>_("Suunnittelijat"),
                               "minimipelaajamaara"=>_("Minimipelaajamäärä"), "maksimipelaajamaara"=>_("Maksimipelaajamäärä"),
                               "saannot"=>_("Säännöt"), "kohdeyleiso"=>_("Kohdeyleisö"),
                               "luoja"=>_("Luoja"), "omistaja_ktunnus"=>_("Omistajan käyttäjätunnus"), "status"=>_("Status"),
                               "sijainti"=>_("Sijainti"), "fiilis"=>_("Fiilis"),
                               "uutuus"=>_("Uutuus"), "mekaniikka"=>_("Mekaniikka"), "idea"=>_("Idea"), "score"=>_("Pisteet"),
                               "sosiaalisuus"=>_("Sosiaalisuus"),"tuuri"=>("Tuuri"), "taktiikka"=>_("Taktiikka"),
                               "strategia"=>_("Strategia"), "id"=>_("Id"));
                    foreach($tiedot as $k=>$v) {
                        switch($k) {
                            case "minimipelaajamaara":
                                $_SESSION["p_minp"]=$v;
                                break;
                            case "maksimipelaajamaara":
                                $_SESSION["p_maxp"]=$v;
                                break;
                            case "kohdeyleiso":
                                $_SESSION["p_kohde"]=$v;
                                break;
                            default:
                                $_SESSION["p_".$k]=$v;
                        }                        
                        printf("<tr><th>%s</th><td>%s</td></tr>\n", $a[$k], $v);
                    }?>
                            
                        </tbody>
                    </table>
                    <button type="button" class="btn" value="muuta"><?php echo _("Muuokkaa");?></button>
                    <table id="scores">
                        <caption><?php echo _("Pelaajien arviot");?></caption>
                        <thead>
                            <tr>
                                <th><?php echo _("Sessio");?></th>
                                <th><?php echo _("Numero");?></th>         
                                <th><?php echo _("Pisteet");?></th>
                                <th><?php echo _("Fiilis");?></th>
                                <th><?php echo _("Mekaniikka");?>
                                <th><?php echo _("Idea");?></th>                               
                                <th><?php echo _("Uutuus");?></th>
                                <th><?php echo _("Sosiaalisuus");?></th>
                                <th><?php echo _("Tuuri");?></th>
                                <th><?php echo _("Taktiikka");?></th>
                                <th><?php echo _("Strategia");?></th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>                                
                            </tr>
                        </tbody>
                          <tfoot>
                            <tr>
                                <th><?php echo _("Sessio");?></th>
                                <th><?php echo _("Numero");?></th>                  
                                <th><?php echo _("Pisteet");?></th>
                                <th><?php echo _("Fiilis");?></th>
                                <th><?php echo _("Mekaniikka");?>
                                <th><?php echo _("Idea");?></th>                               
                                <th><?php echo _("Uutuus");?></th>
                                <th><?php echo _("Sosiaalisuus");?></th>
                                <th><?php echo _("Tuuri");?></th>
                                <th><?php echo _("Taktiikka");?></th>
                                <th><?php echo _("Strategia");?></th>                                
                            </tr>
                        </tfoot>
                    </table>
                </section>
            </div>
        </section>
        <?php include_once("$basepath/footer.html");?>
    </body>
</html>