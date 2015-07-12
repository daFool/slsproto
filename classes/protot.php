<?php
/**
 * Protot
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * */

 /**
  * Protojen rajapinta
  *
  * Protojen tallettaminen, etsiminen, muuttaminen ja poistaminen.
  * */
class PROTOT {
  /** @var handle Database handle **/
    private $db;
    /** @var object SLS Database **/
    private $dbc;
  
    /** @var array Jemma hakutuloksille **/
    private $protot;
    
    /**
     * Constructor
     * @param object $db Database-handle
     * */
    public function __construct($db) {
        $this->db = $db->getHandle();
        $this->dbc = $db;
        
    }
    
    /**
     * Find prototype
     * @param string $Rex SQL ilike expression to search for
     * @param string $FIeld Name of the field to use for the search
     * @return mixed|boolean False if not found and an array containing proto data
     * */
    public function findWithRex($Rex, $Field) {
        try {
            $fields = array("nimi", "omistaja", "suunnittelijat", "luoja", "omistaja_ktunnus");
            if(array_search($Field, $fields)===false) {
                return false;
            }
            $s = "select * from proto where {$Field} ilike :rex;";
            $st = $this->db->prepare($s);
         
            $res = $st->execute(array("rex"=>$Rex));
            if(!$res || $st->rowCount()==0) {
                return false;
            }
            $this->protot = $st->fetchAll();
            return $this->protot;
        }
        catch(PDOException $e) {
            die("Programming error: {$e->getMessage()}");
        }
    }
    
    /**
     * Paginate protot with gusto
     *
     * Datatables:in tarvitsema protohaku. Käyttöoikeuksia varten lisätty parametri, joka kertoo
     * kuka hakeva käyttäjä on. @todo Toteuta käyttöoikeudet!
     * @param int $start Rivi jolta järjestyksessä aloitetaan
     * @param int $length Montako riviä ladataan
     * @param string $order Kenttä jonka mukaan sortataan
     * @param string $search Etsitäänkö jotakin? Ts. filtteröidäänkö sisältöä tällä termillä
     * @param string $kuka Hakijan käyttäjätunnus @todo Toteuta!
     * @return mixed Palautetaan false, mikäli haku itsessään epäonnistuu ja array rivejä muodossa [rivinumero][solunnimi]=solun arvo
     * @todo Pisteytys! Tämän haun pitäisi kohdistua 
     * */
    public function tableFetch($start, $length, $order, $search, $kuka) {
        try {
            $ds = false;
            $tulos = array("lkm"=>0, "protot"=>array(), "riveja"=>0, "filtered"=>0);
            $so="";
            $v="";
            if(isset($search["value"]) && $search["value"]!="") {
                $v = $search["value"];
                $so = "where (nimi ~* :v or omistaja ~* :v or status ~* :v)";
                $ds = true;
            }
            if($order !== false) 
                $oclause="order by $order";
            else
                $oclause="";
                
            $s = "select id, nimi, omistaja, luotu, muokattu, status from proto $so $oclause limit :length offset :start";
            $d = array("length"=>$length, "start"=>$start);
            if($ds) 
                $d["v"]=$v;
            
            $m = "$s ($v)";
            $this->dbc->log($m, __FILE__, __CLASS__,__LINE__,"DEBUG");
            $st = $this->db->prepare($s);
            $res = $st->execute($d);
            if(!$res || $st->rowCount()==0) {
                return $tulos;
            }
            $protot = $st->fetchAll();
            $s = "select count(*) as lkm from proto;";
            $st = $this->db->prepare($s);
            $res = $st->execute();
            if(!$res || $st->rowCount()==0) {
                return $tulos;
            }
            $row = $st->fetch();
            $tulos["lkm"]=$row["lkm"];
            $tulos["protot"]=$protot;
            $tulos["riveja"]=count($protot);
            $tulos["filtered"]=$row["lkm"];
            if($ds) {
                $s = "select count(*) as lkm from protot $so;";
                $st = $this->db->prepare($s);
                $res = $st->execute(array("v"=>$v));
                if($res && $st->rowCount()>0) {
                    $tulos["filtered"]=$st->fetch()["lkm"];
                }
            }
            return $tulos;
        }
        catch(PDOException $e) {
            die("Programming error: {$e->getMessage()}");
        }
    }
    /**
     * Add proto
     * @param array of proto data
     * @return boolean|array False if failed and "tunniste" of inserted row in an array if succeeded
     * */
    public function addProto($proto) {
        try {
            $f = array("nimi"=>"nimi", "omistaja"=>"omistaja", "kuvaus"=>"kuvaus", "suunnittelijat"=>"suunnittelijat", "kesto"=>"kesto", "minp"=>"minimipelaajamaara",
                       "maxp"=>"maksimipelaajamaara", "saannot"=>"saannot", "luoja"=>"luoja", "omistaja_ktunnus"=>"omistaja_ktunnus", "status"=>"status",
                       "kohdeyleiso"=>"kohdeyleiso", "sijainti"=>"sijainti");
            
            $s = "insert into proto(";
            $sv = "values(";
            $first=true;
            $d = array();
            foreach($proto as $k=>$v) {
                if(!isset($f[$k])) {
                    continue;
                }
                if(!$first) {
                    $s.=",";
                    $sv.=",";
                }
                $first=false;
                $s.=$f[$k];
                $sv.=":".$f[$k];
                $d[$f[$k]]=$v;
            }
            $s.=") ".$sv.") returning id;";
            $st = $this->db->prepare($s);
            $res = $st->execute($d);
            if(!$res) {
                return false;
            }
            $r = $st->fetch();
            if(isset($proto["versio"])) {
                $s = "insert into versiot (versio, proto) values (:versio, :proto);";
                $d = array("proto"=>$r['id'], "versio"=>$proto["versio"]);
                $st = $this->db->prepare($s);
                $res = $st->execute($d);
                if(!$res)
                    return false;
            }
            return $r;
        }
        catch(PDOException $e) {
            die("Programming error: {$e->getMessage()}");
        }
    }
    
    /**
     * Autocompleten haku
     * @param str $proto
     * @param str $hakija
     * @return boolean|array False jos ei löytynyt, array jos löytyi
     * */
    public function searchWithNamePart($proto, $hakija) {
        try {
            $proto.='%';
            $s="select nimi from proto where nimi ilike :proto;";
            $st = $this->db->prepare($s);
            $res = $st->execute(array("proto"=>$proto));
            if($res===false)
                return false;
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            if(!is_array($rows))
                $rows = array($row);
            return $rows;
        }
        catch (PDOException $e) {
            die("Programming error: {$e->getMessage()}");
        }
    }
    
}
?>
