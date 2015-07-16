<?php
/**
 * Session lisääminen - lomake
 *
 *  *
 * @uses globals.php
 * @uses common.php
 * @uses uses.php
 * @uses maxrights.php
 * 
 * @package SLS-Proto-tracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * */
require_once("../globals.php");
require_once("$basepath/helpers/common.php");
require_once("$basepath/helpers/users.php");
require_once("$basepath/helpers/maxrights.php");

if(!isset($_SESSION["s_metodi"]) || $_SESSION["s_metodi"]=="") {
    $metodi="lisää";
    $_SESSION["s_sessioid"]="";
}

function def($nimi, $v) {
    echo isset($_SESSION["s_".$nimi]) ? $_SESSION["s_".$nimi] : $v;
}

function chk($nimi, $v) {
    echo isset($_SESSION["s_".$nimi]) && $_SESSION["s_".$nimi]==$v ? "checked" : "";
}

$paluu = "$baseurl/forms/proto.php";
$_SESSION["paluu"]=$paluu;

include_once("$basepath/html_base.html");
?>

    <title><?php echo _("Session lisääminen");?></title>
    <script type="text/javascript">
            function checkEm() {
                var lSessio = document.getElementById("sessio");
                var tunniste = $("#sessioid").val();
                
                if (lSessio.checkValidity()==true) {
                    $("#talleta").removeAttr("disabled");
                    if (typeof tunniste != "undefined" && tunniste) {
                        $("#lpelaaja").removeAttr("disabled");
                        $("#longelma").removeAttr("disabled");
                    }
                }
                else {
                    $("#talleta").attr("disabled", "true");
                    $("#lpelaaja").attr("disabled", "true");
                    $("#longelma").attr("disabled", "true");
                }
                $("#on_proto").val($("#proto").val());
            }
        
        $(function () {
              
            $("#sessio").find("input, textarea, select").each(function () {
                $(this).blur(function () {
                    checkEm();
                });
            });
            
            $("#proto").autocomplete( {
                source : "<?php echo $baseurl;?>/json_proto.php",
                minlength : 2,
                select : function (event, ui) {
                    checkEm();
                }
            })
            
            
            $("#versio").autocomplete( {
                source : "<?php echo $baseurl;?>/json_versio.php",
                minlength : 2,
                select : function (event, ui) {
                    checkEm();
                }
            })
            
            $("#pe_tunnus").autocomplete( {
                source : "<?php echo $baseurl;?>/json_tunniste.php",
                minlength: 2
            })
            tPelaajat = $("#pelaajat").DataTable ( {
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "orderMulti": true,
                "search": {
                "regex": true,
                "caseInsensitive": true,
                "smart" : true},
                "ajax": "<?php echo $baseurl;?>/json_pelaajat.php",
                <?php include("$basepath/datatables_language.js");?>
              
            });
            
            tOngelmat = $("#ongelmat").DataTable ( {
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "orderMulti": true,
                "search": {
                "regex": true,
                "caseInsensitive": true,
                "smart" : true},
                "ajax": "<?php echo $baseurl;?>/json_ongelmat.php",
                <?php include("$basepath/datatables_language.js");?>
              
            })
            
            $("#pelaajat tbody").on('click','tr', function () {
                var numero=$(this).children("td:first").html();
                $.post("<?php echo $baseurl;?>/json_pelaaja.php", {"numero" : numero}, function (data) {
                    if (data["virhe"]==true) {
                        $("#palaute").removeClass("alert-success");
                        $("#palaute").addClass("alert-danger");
                        $("#palaute_t").html(data["virheet"]);
                        $("#palaute").show();
                        console.log(data);
                        return;
                    }
                    
                    $.each(data.data, function(index, value) {
                        switch(index) {
                            case "numero":
                                index="jrnro"
                                break;
                            case "sessio":
                                break;
                            case "kelle":
                                if (value.indexOf("perheille")!=-1) {
                                    $("#pe_perheet").prop("checked", true);
                                }
                                if (value.indexOf("kasuaalit")!=-1) {
                                    $("#pe_kasuaalit").prop("checked", true);
                                    //code
                                }
                                if (value.indexOf("pelaajat")!=-1) {
                                    $("#pe_pelaajat").prop("checked", true);
                                    //code
                                }
                                if (value.indexOf("lapset")!=-1) {
                                    $("#pe_lapset").prop("checked", true);
                                    //code
                                }
                                if (value.indexOf("nuoret")!=-1) {
                                    $("#pe_nuoret").prop("checked", true);
                                    //code
                                }
                                if (value.indexOf("aikuisille")!=-1) {
                                    $("#pe_aikuiset").prop("checked", true);
                                    //code
                                }
                                break;
                            default:
                                var i="#pe_"+index;
                                $(i).val(value);
                        }
                    })
                    $("#pelaaja").modal("show");
                    
                })
            })
            // console.log(tPelaajat);    
        });
  
        function Talleta() {                    
            $.post("<?php echo $baseurl;?>/talletaSessio.php", $("#sessio").serialize(), function (data){
                console.log("Succes");
                if (data["virhe"]==false) {
                    viesti="<?php echo _("Istunto talletettu: ");?>"+Date().toLocaleString();
                    $("#palaute").removeClass("alert-danger");
                    $("#palaute").addClass("alert-success");
                    $("#palaute_t").html(viesti);
                    $("#palaute").show();
                    console.log(data);
                    $("#sessioid").val(data["data"]["sessioid"]);
                    console.log(data.data.sessioid);
                    checkEm();
                    tOngelmat.ajax.reload();
                } else {
                    $("#palaute").removeClass("alert-success");
                    $("#palaute").addClass("alert-danger");
                    $("#palaute_t").html(data["virheet"]);
                    $("#palaute").show();
                }
            }).fail(function (data) {
                console.log("Fail");
                console.log(data);
            });
        }
        function nyt(kohde) {
            var nyt=new Date();
            var arvo=nyt.toISOString().substring(0,16);
            function pad(number) {
                if (number < 10) {
                    return '0'+number;
                    //code
                }
                return number;
            }
            arvo = nyt.getFullYear()+"-"+pad(nyt.getMonth()+1)+"-"+pad(nyt.getDate())+"T"+
                pad(nyt.getHours())+":"+pad(nyt.getMinutes());
            kohde="#"+kohde;
            $(kohde).val(arvo);
        }
        
        function talletaOngelma() {
            var ongelma = document.getElementById("ongelmaF");
            
            if (ongelma.checkValidity()==false) {
                return;
            }
            $('#ongelma').modal('hide');
            $.post("<?php echo $baseurl;?>/talletaOngelma.php", $("#ongelmaF").serialize(), function (data) {
                if (data["virhe"]==false) {
                    viesti="<?php echo _("Ongelma talletettu: ");?>"+Date().toLocaleString();
                    $("#palaute").removeClass("alert-danger");
                    $("#palaute").addClass("alert-success");
                    $("#palaute_t").html(viesti);
                    $("#palaute").show();
                    tOngelmat.ajax.reload();
                }
                else {
                    $("#palaute").removeClass("alert-success");
                    $("#palaute").addClass("alert-danger");
                    $("#palaute_t").html(data["virheet"]);
                    $("#palaute").show();
                }
                console.log(data);
            }).fail(function (data) {
                console.log("Ongelman talletus mätti!");
                console.log(data);
            });            
        }
        
        
    </script>
    </head>
    <body>
        <?php include_once("$basepath/navbar.html"); ?>
        <div class="alert alert-dismissible" roles="alert" id="palaute" hidden="true">
            <button type="button" class="close" onclick="$('#palaute').hide()" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span id="palaute_t"></span>
        </div>
        <section class='container'>
            <div class="row">
                <section class='col-xs-12 col-sm-6 col-md-6'>
                    <h2><?php echo _("Session lisääminen / muokkaaminen");?></h2>
                    <form name="sessio" id="sessio" method="POST" action="<?php echo "$baseurl/talletaSessio.php";?>">
                        <label for="sessioid"><?php echo _("Tunniste");?>
                            <input type="text" id="sessioid" name="sessioid" value="<?php def("sessioid","");?>" readonly class="form-control"/>
                        </label>
                        <label for="ajankohta"><?php echo _("Päivä: ");?>
                            <div class="input-group">
                                <input type="datetime-local" id="ajankohta" name="ajankohta" required="true" value="<?php def("ajankohta", "");?>" class="form-control">
                                <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('ajankohta');"><?php echo _("Nyt");?></button></span>
                            </div>
                        </label>
                        <label for="proto"><?php echo _("Proto:");?>
                            <input type="text" id="proto" name="proto" required="true" value="<?php def("proto","");?>" class="form-control">
                        </label>
                         <label for="versio"><?php echo _("Versio:");?>
                            <input type="text" id="versio" name="versio" required="true" value="<?php def("versio","");?>" class="form-control">
                        </label>
                       
                        <fieldset>
                            <label><?php echo _("Säännöt luettu etukäteen: ");?>
                                <input type="radio" name="saannotluettu" id="saannotluettu" value="kyllä" <?php chk("saannotluettu", "kyllä");?>/><?php echo _("Kyllä");?>
                                <input type="radio" name="saannotluettu" id="saannotluettu" value="silmäilty" <?php chk("saannotluettu", "silmäilty");?>/><?php echo _("Silmäilty");?>
                                <input type="radio" name="saannotluettu" id="saannotluettu" value="ei" <?php chk("saannotluettu", "ei");?> /><?php echo _("Ei");?>
                            </label>
                        </fieldset>
                        <label for="selitysA"><?php echo _("Sääntöselitys alkoi ja loppui: ");?></label>
                        <div class="input-group">                            
                            <input id="selitysA" name="saantoselitys_alkoi" type="datetime-local" value="<?php def("saantoselitys_alkoi","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('selitysA');"><?php echo _("Nyt");?></button></span>
                            <input id="selitysL" name="saantoselitys_loppui" type="datetime-local" value="<?php def("saantoselitys_loppui","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('selitysL');"><?php echo _("Nyt");?></button></span>
                        </div>
                        <label for="setupA"><?php echo _("Setup alkoi ja loppui: ");?></label>
                        <div class="input-group">
                            <input id="setupA" name="setup_alkoi" type="datetime-local" value="<?php def("setup_alkoi","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('setupA')"><?php echo _("Nyt");?></button></span>
                            <input id="setupL" name="setup_loppui" type="datetime-local" value="<?php def("setup_loppui","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('setupL')"><?php echo _("Nyt");?></button></span>
                        </div>
                        <label for="peliA"><?php echo _("Peli alkoi ja loppui: ");?></label>
                        <div class="input-group">
                            <input id="peliA" name="peli_alkoi" type="datetime-local" value="<?php def("peli_alkoi","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('peliA')"><?php echo _("Nyt");?></button></span>
                            <input id="peliL" name="peli_loppui" type="datetime-local" value="<?php def("peli_loppui","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('peliL')"><?php echo _("Nyt");?></button></span>
                        </div>
                        <label for="vkesto"><?php echo _("Vuoron kesto sekunneissa: ");?>
                            <input id="vkesto" name="vuoron_kesto" type="number" min=0 max=480 step=5 value="<?php def("vuoron_kesto",0);?>" class="form-control"/>
                        </label>
                        <label for="kkesto"><?php echo _("Kierroksen (1 vuoro/pelaaja) kesto minuuteissa: ");?>
                            <input id="kkesto" name="kierroksen_kesto" type="number" min=0 max=480 step=5 value="<?php def("kierroksen_kesto",0);?>" class="form-control"/>
                        </label>
                        <label for="kierroksia"><?php echo _("Kierroksia (1 vuoro/pelaaja): ");?>
                            <input id="kierroksia" name="kierroksia" type="number" min=0 max=480 step=1 value="<?php def("kierroksia",0);?>" class="form-control"/>
                        </label><br/>
                        <label for="loppuA"><?php echo _("Lopputoimet alkoivat: ");?></label>
                        <div class="input-group">
                            <input id="loppuA" name="lopputoimet_alkoivat" type="datetime-local" value="<?php def("lopputoimet_alkoivat","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('loppuA')"><?php echo _("Nyt");?></button></span>
                            <input id="loppuL" name="lopputoimet_loppuivat" type="datetime-local" value="<?php def("lopputoimet_loppuivat","");?>" class="form-control">
                            <span class="input-group-btn"><button class="btn btn-default" type="button" onclick="nyt('loppuL')"><?php echo _("Nyt");?></button></span>
                        </div>
                        <label for="kuvaus"><?php echo _("Kuvaus: ");?></label>
                        <textarea name="kuvaus" id="kuvaus" rows="4" cols="80" placeholder="<?php echo _("Mitä tapahtui? Miksi?");?>"><?php echo def("kuvaus","");?></textarea>
                        <button disabled="true" type="button" class="btn" id="lpelaaja" data-toggle="modal" data-target="#pelaaja"><?php echo _("Lisää pelaaja");?></button>
                        <button disabled="true" type="button" class="btn" id="longelma" data-toggle="modal" data-target="#ongelma"><?php echo _("Lisää ongelma");?></button>                        
                        <button disabled="true" type="button" class="btn" id="talleta" onclick="Talleta();"><?php echo _("Talleta");?></button>
                        <button type="button" class="btn" id="valmis"><?php echo _("Valmis");?></button>
                    </form>
                    <table id="pelaajat" name="pelaajat">
                        <caption><?php echo _("Pelaajat");?></caption>        
                        <thead>
                            <tr>
                                <th><?php echo _("Aloituspaikka");?></th>
                                <th><?php echo _("Nimi");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?php echo _("Aloituspaikka");?></th>
                                <th><?php echo _("Nimi");?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <table id="ongelmat" name="ongelmat">
                        <caption><?php echo _("Ongelmat");?></caption>      
                        <thead>
                            <tr>
                                <th><?php echo _("Tunniste");?></th>
                                <th><?php echo _("Kuvaus");?></th>
                                <th><?php echo _("Laji");?></th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?php echo _("Tunniste");?></th>
                                <th><?php echo _("Kuvaus");?></th>
                                <th><?php echo _("Laji");?></th>
                            </tr>
                        </tfoot>
                    </table>
                </section>
            </div>
            <div class="modal fade" id="pelaaja" tabindex="-1" role="dialog" aria-labelledby="pelaajaLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" onclick="$('#pelaaja').modal('hide')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="pelaajaLabel"><?php echo _("Pelaaja");?></h4>
                        </div>
                        <div class="modal-body">
                            <form name="f_pelaaja" id="f_pelaaja" method="post" action="<?php echo $baseurl;?>/talletaPelaaja.php">
                                <label for="pe_jrnro"><?php echo _("Järjestysnumero");?>
                                    <input type="number" min="1" max="20" step="1" id="pe_jrnro" name="pe_jrnro" class="form-control" required="true"/>
                                </label>
                                <label for="pe_tunnus"><?php echo _("Käyttäjätunnus");?>
                                    <input type="text" size="20" maxlength="255" id="pe_tunnus" name="pe_tunnus" class="form-control" />
                                </label>
                                <label for="pe_sijoitus"><?php echo _("Sijoitus");?>
                                    <input type="number" min="1" max="20" step="1" id="pe_sijoitus" name="pe_sijoitus" class="form-control" />
                                </label>
                                <label for="pe_tulos"><?php echo _("Tulos");?>
                                    <input type="text" id="pe_tulos" name="pe_tulos" size="10" maxlength="255" class="form-control" />
                                </label>
                                <label for="pe_nimi"><?php echo _("Nimi");?>
                                    <input type="text" id="pe_nimi" name="pe_nimi" size="50" maxlength="255" class="form-control" />
                                </label>
                                <div>
                                    <label for="pe_aiemmin"><?php echo _("Oletko aiemmin pelannut?");?></label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_aiemmin" name="pe_aiemmin" value="kyllä" class="radio"/>
                                        <?php echo _("Kyllä");?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_aiemmin" name="pe_aiemmin" value="ei" class="radio"/>
                                        <?php echo _("En");?>
                                    </label>
                                    <label for="pe_uudestaan"><?php echo _("Pelaisitko uudestaan?");?></label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_uudestaan" name="pe_uudestaan" value="kyllä" class="radio"/>
                                        <?php echo _("Kyllä");?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_uudestaan" name="pe_uudestaan" value="ei" class="radio"/>
                                        <?php echo _("En");?>
                                    </label>
                                    <label for="pe_ostaisitko"><?php echo _("Ostaisitko pelin kauapasta?");?></label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_ostaisitko" name="pe_ostaisitko" value="kyllä" class="radio"/>
                                        <?php echo _("Kyllä");?>
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" id="pe_ostaisitko" name="pe_ostaisitko" value="ei" class="radio" />
                                        <?php echo _("En");?>
                                    </label>
                                </div>    
                                <datalist id="pe_sosiaalisuus">
                                    <option>0</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </datalist>
                                <label for="pe_sosiaalisuus"><?php echo _("Sosiaalisuus");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_sosiaalisuus" name="pe_sosiaalisuus" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_tuuri"><?php echo _("Tuuri");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_tuuri" name="pe_tuuri" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_taktiikka"><?php echo _("Taktiikka");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_taktiikka" name="pe_taktiikka" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_fiilis"><?php echo _("Strategia");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_strategia" name="pe_strategia" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_fiilis"><?php echo _("Fiilis");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_fiilis" name="pe_fiilis" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_uutuus"><?php echo _("Uutuus");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_uutuus" name="pe_uutuus" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_mekaniikka"><?php echo _("Mekaniikka");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_mekaniikka" name="pe_mekaniikka" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_idea"><?php echo _("Idea");?>
                                    <input type="range" min="0" max="5" step="1" id="pe_idea" name="pe_idea" list="pe_sosiaalisuus" value="3"/>
                                </label>
                                <label for="pe_kelle"><?php echo ("Kelle peli on tarkoitettu?");?></label>
                                <div class="input-group">
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_perheet" id="pe_perheet" value="perheille" class="checkbox"/>
                                        <?php echo _("Perheille");?>
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_kasuaalit" id="pe_kasuaalit" value="kasuaaleille" class="checkbox"/>
                                        <?php echo _("Kasuaaleille");?>
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_pelaajat" id="pe_pelaajat" value="pelaajat" class="checkbox"/>
                                        <?php echo _("Pelaajille");?>
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_lapset" id="pe_lapset" value="lapset" class="checkbox"/>
                                        <?php echo _("Lapsille");?>
                                    </label>
                                        <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_nuoret" id="pe_nuoret" value="nuoret" class="checkbox"/>
                                        <?php echo _("Nuorille");?>
                                    </label>
                                        <label class="checkbox-inline">
                                        <input type="checkbox" name="pe_aikuiset" id="pe_aikuiset" value="aikuisille" class="checkbox"/>
                                        <?php echo _("Aikuisille");?>
                                    </label>                                                               
                                </div>
                                <label for="pe_assosiaatiot">
                                    <?php echo _("Mitä peliä/pelejä tämä muistuttaa?");?>
                                </label>
                                <input type="text" size="50" id="pe_assosiaatiot" name="pe_assosiaatiot" class="form-control"/>
                                <label for="pe_kokemus"><?php echo _("Kokemus pelaajana");?>
                                    <select name="pe_kokemus" id="pe_kokemus">
                                        <option value="0">Ensikertalainen</option>
                                        <option value="1">Aloittelija</option>
                                        <option value="2">Kasuaali</option>
                                        <option value="3">Harrastaja</option>
                                        <option value="4">Pelaaja</option>
                                        <option value="5">Fanaatikko</option>                                    
                                    </select>
                                </label>
                                <button type="button" class="btn" id="pe_talleta" name="pe_talleta" data-toggle="modal" data-target="pelaaja" onclick="talletaPelaaja();"><?php echo _("Talleta");?></button>
                                <button type="button" class="btn" id="pe_sulje" name="pe_sulje" onclick="$('#pelaaja').modal('hide')"><?php echo _("Sulje");?></button>
                            </form> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="ongelma" tabindex="-1" role="dialog" aria-labelledby="ongelmaLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="ongelmaLabel"><?php echo _("Ongelma");?></h4>
                        </div>
                        <div class="modal-body">
                            <form name="ongelmaF" id="ongelmaF" method="POST">
                                    <label for="on_id"><?php echo _("Tunniste");?></label>
                                    <input type="number" readonly name="on_id" id="on_id" class="form-control"/>
                                    <label for="on_proto"><?php echo _("Proto");?></label>
                                    <input type="text" readonly name="on_proto" id="on_proto" class="form-control"/>
                                    <label for="on_kuvaus">
                                        <?php echo _("Ongelman kuvaus:");?>
                                        <textarea class="textarea" name="on_kuvaus" id="on_kuvaus" rows="4" cols="60"></textarea>
                                    </label>
                                    <label for="on_laji">
                                        <?php echo _("Ongelman laji:");?>
                                    </label>
                                    <select id="on_laji" name="on_laji">
                                        <option value="kehitysidea"><?php echo _("Kehitysidea");?></option>
                                        <option value="sääntövirhe"><?php echo _("Sääntövirhe");?></option>
                                        <option value="komponenttivirhe"><?php echo _("Komponenttivirhe");?></option>
                                    </select>
                                    <button type="button" class="btn" id="on_talleta" name="on_talleta" onclick="talletaOngelma()"><?php echo _("Talleta");?></button>
                                    <button type="button" class="btn" id="on_sulje" name="on_sulje" onclick="$('#ongelma').modal('hide')"><?php echo _("Sulje");?></button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>