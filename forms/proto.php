<?php
/**
 * Proton lisääminen - lomake
 *
 *  *
 * @uses globals.php
 * @uses common.php
 * @uses uses.php
 * @uses maxrights.php
 * 
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * */
require_once("../globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/helpers/maxrights.php");

if(!isset($_SESSION["p_metodi"]) || $_SESSION["p_metodi"]=="") {
    $metodi="lisää";
    $status_allowed=true;
}
else {
    $metodi="muuta";
    $status_allowed=false;
}

function def($nimi, $v) {
    echo isset($_SESSION["p_".$nimi]) ? $_SESSION["p_".$nimi] : $v;
}
$paluu = "$baseurl/forms/proto.php";
$_SESSION["paluu"]=$paluu;

include_once("$basepath/html_base.html");

?>
    <script type="text/javascript">
        $(function() {
            function checkEm() {
                var lomake = document.getElementById("proto");
                console.log("Tsekattu");
                if (lomake.checkValidity()==true) {
                    $("#tallenna").removeAttr("disabled");
                }
                else {
                    $("#tallenna").attr("disabled", "true");
                }
                $("#proto").find(":invalid").each(function () {
                    console.log($(this).attr('id')+" on rikki");
                })
            }
            
            $("#omistaja").autocomplete({
                source : "<?php echo $baseurl; ?>/json_tunniste.php",
                minlength: 2
            })
            

            $("#proto").find("input, textarea, select").each(function () {
                console.log($(this).attr('id'));
                $(this).blur(function () {
                    checkEm();
                });
            });
            
            $("#tallenna").on('click', function() {
                console.log("Submit");
                $("#proto").submit();                
            });
            
            $("#vbutton").on('click', function () {
                $("#varoitus").hide();
            })

            <?php
            if(isset($_SESSION["p_virhe"]) && $_SESSION["p_virhe"]!="") {
                ?>
                $("#warning").html("<?php echo $_SESSION["p_virhe"];?>");
                $("#varoitus").show();
                <?php
            } ?>
        })
    </script>

    <title><?php echo _("Proton lisääminen");?></title>
    </head>
    <body>
        <?php include_once("$basepath/navbar.html"); ?>
        <section class='container'>
            <div class="row">
                <section class='col-xs-12 col-sm-6 col-md-6'>
                    <h2><?php echo _("Proton lisääminen / muokkaaminen");?></h2>
                    <form name="proto" id="proto" method="POST" action="<?php echo "$baseurl/lisaaProto.php";?>">
                        <input type="hidden" name="metodi" id="metodi" value="<?php echo $metodi;?>">
                        <label for="nimi"><?php echo _("Nimi: ");?>
                            <input class="span3" type="text" name="nimi" required="true" size="40" maxlength="255" id="nimi" value="<?php def("nimi","");?>" placeholder="<?php echo _("Avaruuden valloitus");?>"/>
                        </label>
                        <label for="omistaja"><?php echo _("Omistaja: ");?>
                            <input type="text" name="omistaja" id="omistaja" required="true" size="40" maxlength="255" value="<?php def("omistaja","");?>" />
                        </label>
                        <label for="suunnittelijat"><?php echo _("Suunnittelijat: ");?>
                            <input type="text" name="suunnittelijat" id="suunnittelijat" required="true" size="40" maxlength="255" value="<?php def("suunnittelijat","");?>" />                            
                        </label>
                        <label for="kesto"><?php echo _("Kesto minuuteissa: ");?>
                            <input type="number" name="kesto" id="kesto" required="true" min=0 max=480 value="<?php def("kesto",0);?>" />
                        </label>
                        <label for="minp"><?php echo _("Minimimäärä pelaajia: ");?>
                            <input type="number" name="minp" id="minp" required="true" min=1 max=20 value="<?php def("minp",4);?>"  />                            
                        </label>
                        <label for="maxp"><?php echo _("Maksimimäärä pelaajia: ");?>
                            <input type="number" name="maxp" id="maxp" required="true" min=1 max=20 value="<?php def("maxp",6);?>" />
                        </label>
                        <label for="saannot"><?php echo _("Sääntöjen osoite: ");?>
                            <input type="url" name="saannot" id="saannot" value="<?php def("saannot","");?>" maxlength="255" size="80"/>
                        </label>
                        <label for="kohdeyleiso"><?php echo _("Pelin kohdeyleisö: ");?>
                            <input type="text" name="kohde" id="kohde" value="<?php def("kohde", "");?>" maxlength="255" size="80"
                                                                                                     placeholder="<?php echo _("Nuoret aikuiset, 12- vuotta");?>"/>
                        </label>
                        <label for="versio"><?php echo _("Versio: ");?>
                            <input type="text" name="versio" id="versio" value="<?php def("versio","");?>" maxlength="255" size="80" />
                        </label>
                        <?php
                            if($status_allowed) {
                                ?>
                            <label for="status"><?php echo _("Proton status: ");?>
                                <select name="status" id="status" required="true">
                                    <option value="public" <?php echo isset($_SESSION["p_status"]) && $_SESSION["p_status"]=="public" ? 'selected="selected"' : "";?>><?php echo _("Julkinen");?></option>
                                    <option value="private"<?php echo isset($_SESSION["p_status"]) && $_SESSION["p_status"]=="private" ? 'selected="selected"' : "";?>><?php echo _("Yksityinen");?></option>
                                    <option value="limited" <?php echo isset($_SESSION["p_status"]) && $_SESSION["p_status"]=="limited" ? 'selected="selected"' : "";?>><?php echo _("Rajoitettu");?></option>
                                </select>
                            </label>
                            <?php 
                            }
                            ?>
                        <label for="kuvaus"><?php echo _("Kuvaus: ");?>
                            <textarea cols="80" rows="4" id="kuvaus" name="kuvaus"><?php echo def("kuvaus","");?></textarea>
                        </label>
                        <label for="sijainti"><?php echo _("Sijainti: ");?>
                            <input type="text" name="sijainti" id="sijainti" value="<?php echo def("sijainti","");?>" size="80" maxlength="255"/>
                        </label>
                        <button type="button" class="btn btn-lg btn-default" id="tallenna" disabled="true"><?php echo _("Tallenna");?></button>
                    </form>
                    <div class="alert alert-warning collapse" role="alert" id="varoitus">
                                <button type="button" class="close" id="vbutton" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <span id="warning">Varoitus</span>
                    </div>
                </section>
            </div>
        </section>
        <?php include_once("$basepath/footer.html");?>
    </body>