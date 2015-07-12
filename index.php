<?php
/**
 * Pääsivu
 *
 * Näytetään Datatablesilla kaikki protot ja sessiot, joita kannasta löytyy.
 * Jos on kirjautunut ja on oikeuksia protoon, pääsee protoa klikkaamalla katselemaan protoa ja sessiota klikkaamalla katselemaan sessiota.
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * @uses globals.php
 * @users users.php
 * @uses common.php
 */

 require_once("globals.php");
 require_once("$basepath/helpers/common.php");
 require_once("$basepath/helpers/users.php");
 include_once("$basepath/html_base.html");
?>
    <title><?php echo _("Prototracker");?></title>
    <script type="text/javascript">
        $(document).ready(function() {
            // Protot-taulu
            $('#protot').dataTable( {
                "processing" : true,
                "serverSide" : true,
                "responsive" : true,
                "orderMulti" : true,
                "search" : {
                    "regex" : true,
                    "casInsensitive" : true,
                    "smart" : true},
                "ajax" : "<?php echo "$baseurl/json_protot.php";?>",
                <?php include("$basepath/datatables_language.js");?>
                }
            );
        
            // Sessiot-taulu
            $('#sessiot').dataTable( {
                "processing" : true,
                "serverSide" : true,
                "responsive" : true,
                "orderMulti" : true,
                "search" : {
                    "regex" : true,
                    "casInsensitive" : true,
                    "smart" : true},
                "ajax" : "<?php echo "$basepath/json_sessiot.php";?>",
                <?php include("$basepath/datatables_language.js");?>
                }
            );
        });                
    </script>
    </head>
    <body>
        <?php include_once("navbar.html");?>
        <section class="container">
            <div class="row">
                <section class="col-xs-12 col-sm-6 col-md-6">
                    <h2><?php echo _("Protot");?></h2>
                    <table id="protot" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo _("Id");?></th>
                                <th><?php echo _("Nimi");?></th>
                                <th><?php echo _("Omistaja");?></th>
                                <th><?php echo _("Lisätty");?></th>
                                <th><?php echo _("Muokattu");?></th>
                                <th><?php echo _("Tila");?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><?php echo _("Id");?></th>
                                <th><?php echo _("Nimi");?></th>
                                <th><?php echo _("Omistaja");?></th>
                                <th><?php echo _("Lisätty");?></th>
                                <th><?php echo _("Muokattu");?></th>
                                <th><?php echo _("Tila");?></th>
                            </tr>
                        </tfoot>                      
                    </table>
                </section>
                <section class="col-xs-12 col-sm-6 col-md-6">
                    <h2><?php echo _("Sessiot");?></h2>
                    <table id="sessiot" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo _("Ajankohta");?></th>
                                <th><?php echo _("Proto");?></th>
                                <th><?php echo _("Vetäjä");?></th>
                                <th><?php echo _("Pelaajia");?></th>
                                <th><?php echo _("Kesto");?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th><?php echo _("Ajankohta");?></th>
                                <th><?php echo _("Proto");?></th>
                                <th><?php echo _("Vetäjä");?></th>
                                <th><?php echo _("Pelaajia");?></th>
                                <th><?php echo _("Kesto");?></th>
                            </tr>
                        </tfoot>
                        </table>
                </section>
            </div>
        </section>
        <?php include_once("$basepath/footer.html");?>
    </body>
</html>