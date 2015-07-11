<?php
/**
 * Protot
 *
 * @package SLS-Proto-tracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * @uses globals.php
 * @uses users.php
 * @uses common.php
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
    /** @var object Current game(s)
     * */
    private $games;
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
     * @return mixed|boolean False if not found and an array containing game data
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
            $this->games = $st->fetchAll();
            return $this->games;
        }
        catch(PDOException $e) {
            die("Programming error: {$e->getMessage()}");
        }
    }
    
    /**
     * Paginate games with gusto
     * */
    public function tableFetch($start, $length, $order, $search) {
        try {
            $ds = false;
            $tulos = array("lkm"=>0, "pelit"=>array(), "riveja"=>0, "filtered"=>0);
            $so="";
            $v="";
            if(isset($search["value"])) {
                $v = $search["value"];
                $so = "where (nimi ~* :v or julkaisija ~* :v or suunnittelija ~* :v or bgglinkki ~* :v)";
                $ds = true;
            }
            if($order !== false) {
                $s = "select * from peli $so order by $order limit :length offset :start;";
                $d = array("length"=>$length, "start"=>$start);
            } else {
                $s = "select * from peli $so limit :length offset :start;";
                $d = array("length"=>$length, "start"=>$start);
            }
            if($ds) 
                $d["v"]=$v;
            
            $m = "$s ($v)";
            $this->dbc->log($m, __FILE__, __CLASS__,__LINE__,"DEBUG");
            $st = $this->db->prepare($s);
            $res = $st->execute($d);
            if(!$res || $st->rowCount()==0) {
                return $tulos;
            }
            $pelit = $st->fetchAll();
            $s = "select count(*) as lkm from peli;";
            $st = $this->db->prepare($s);
            $res = $st->execute();
            if(!$res || $st->rowCount()==0) {
                return $tulos;
            }
            $row = $st->fetch();
            $tulos["lkm"]=$row["lkm"];
            $tulos["pelit"]=$pelit;
            $tulos["riveja"]=count($pelit);
            $tulos["filtered"]=$row["lkm"];
            if($ds) {
                $s = "select count(*) as lkm from peli $so;";
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
    
}
?>
