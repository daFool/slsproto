<?php
/**
 * Ongelmat
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 *
 * */

 /**
  * Ongelmien rajapinta
  *
  * Ongelmien tallettaminen, etsiminen, muuttaminen ja poistaminen.
  * */
class ONGELMA {
  /** @var handle Database handle **/
    private $db;
    /** @var object SLS Database **/
    private $dbc;
    /** @var object Current game(s)
     * */
    /**
     * Constructor
     * @param object $db Database-handle
     * */
    public function __construct($db) {
        $this->db = $db->getHandle();
        $this->dbc = $db;
        
    }
    
     /**
     * Ongelman tietojen talletus
     * @param int $proto Proton tunniste
     * @param int $id Ongelman tunniste
     * @param array $d Loput datasta
     * @return boolean True jos onnistui, False jos epäonnistui
     * */
    public function talletaOngelma($proto, $id, $d) {
        try {
            if($id!="") {
                $s = "select count(*) as lkm from ongelma where proto=:proto and id=:id;";
                $da = array("proto"=>$proto, "id"=>$id);
                $st = $this->db->prepare($s);
                $res = $st->execute($da);
                if($res===false)
                    return false;
                $s1="update ongelma set muutettu=now()";
                $s2=" where proto=:proto and id=:id;";
                if(isset($d["kuvaus"])) {
                    $s1.=", kuvaus=:kuvaus";
                }
                if(isset($d["laji"])) {
                    $s1.=", laji=:laji";
                }
                $s=$s1." ".$s2;
                $d["id"]=$id;
                $insert=false;
            }
            else {
                $s1="insert into ongelma (proto, luotu";
                $s2=" values (:proto, now()";
                if(isset($d["kuvaus"])) {
                    $s1.=", kuvaus";
                    $s2.=", :kuvaus";
                }
                if(isset($d["laji"])) {
                    $s1.=", laji";
                    $s2.=", :laji";
                }
                $s=$s1.") ".$s2.") returning id;";
                $insert=true;
            }
            
            $d["proto"]=$proto;
            $st=$this->db->prepare($s);
            $res=$st->execute($d);
            if($res==false)
                return false;
            
            if($insert===false)
                return true;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row["id"];
        }
        catch(PDOException $pe) {
            die($pe->getMessage());
            
        }
    }
    
    /**
     * Istuntoon liittyvät pelaajat
     * @param int $start Mistä pelaajasta alkaen
     * @param int $length Montako pelaajaa
     * @param string $order Järjestyskenttä
     * @param string $search Mitä haetaan
     * @param int $proto Proton tunniste
     * @return array|boolean Joko löydetyt pelaajat tahi false
     * */
    public function tableFetch($start, $length, $order, $search, $proto) {
        try {
            $ds = false;
            $tulos = array("lkm"=>0, "ongelmat"=>array(), "riveja"=>0, "filtered"=>0);
            $o="";
            $v="";
            if(isset($search["value"])) {
                $v = $search["value"];
                $so = "and (kuvaus ~* :v or laji=:v)";
                $ds=true;
            }
            $d = array("length"=>$length, "start"=>$start, "proto"=>$proto);
            if($order !== false) {
                $s = "select id, kuvaus, laji from ongelma where proto=:proto $so order by $order limit :length offset :start;";                
            } else {
                $s = "select id, kuvaus, laji from ongelma where proto=:proto $so limit :length offset :start;";
            }
            if($ds)
                $d["v"]=$v;
            $st = $this->db->prepare($s);
            $res = $st->execute($d);
            if(!$res || $st->rowCount()==0)
                return $tulos;
            $ongelmat = $st->fetchAll();
            $s = "select count(*) as lkm from ongelma where proto=:proto;";
            $st = $this->db->prepare($s);
            $res = $st->execute(array("proto"=>$proto));
            if(!$res || $st->rowCount()==0)
                return $tulos;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            $tulos["lkm"]=$row["lkm"];
            $tulos["ongelmat"]=$ongelmat;
            $tulos["riveja"]=count($ongelmat);
            $tulos["filtered"]=$row["lkm"];
            if($ds) {
                $s = "select count(*) as lkm from ongelma where proto=:proto $so;";
                $st = $this->db->prepare($s);
                $res = $st->execute(array("v"=>$v, "proto"=>$proto));
                if($res && $st->rowCount()>0)
                    $tulos["filtered"]=$st->fetch()["lkm"];
            }
            return $tulos;
        }
        catch(PDOException $pe) {
            die($pe->getMessage());
        }
    }
    
    /**
     * Ongelman tiedot
     * @param int $id Ongelman tunniste
     * @return array|boolean False jos ei löytynyt, tiedot jos löytyi
     * */
    public function haeOngelma($id) {
        try {
            $s = "select kuvaus,laji from ongelma where id=:id;";
            $st = $this->db->prepare($s);
            $d = array("id"=>$id);
            $res = $st->execute($d);
            if($res===false)
                return false;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        catch(PDOException $e) {
            die($e->getMessage());
        }
    }
}
?>