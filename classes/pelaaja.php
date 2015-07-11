<?php
/**
 * Pelaajat
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
  * Pelaajien rajapinta
  *
  * Pelaajien tallettaminen, etsiminen, muuttaminen ja poistaminen.
  * */
class PELAAJA {
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
     * Pelaajan tietojen talletus
     * @param int $sessio Istunnon tunniste
     * @param int $pelaaja Pelaajan järjestysnumero
     * @param array $d Loput datasta
     * @return boolean True jos onnistui, False jos epäonnistui
     * */
    public function talletaPelaaja($sessio, $pelaaja, $d) {
        try {
            $s = "select count(*) as lkm from pelaaja where sessio=:sessio and numero=:numero;";
            $da = array("sessio"=>$sessio, "numero"=>$pelaaja);
            $st = $this->db->prepare($s);
            $res = $st->execute($da);
            if($res===false)
                return false;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if($row["lkm"]==0) {
                $s1 = "insert into pelaaja (sessio, numero ";
                $s2 = ") values (:sessio, :numero";
                $u=false;
                $f=false;
            } else {
                $s1 = "update pelaaja set ";
                $s2 = " where sessio=:sessio and numero=:numero;";
                $u=true;
                $f=true;
            }
            
            foreach($d as $k=>$v) {
                if($f==true) {
                    $s1.="$k=:$k";
                    $f=false;
                    continue;
                }
                if($u==true) {
                    $s1.=",$k=:$k";
                } else {
                    $s1.=",$k";
                    $s2.=",:$k";
                }
            }
            $d["sessio"]=$sessio;
            $d["numero"]=$pelaaja;
            $s=$s1.$s2;
            if($u==false)
                $s.=");";
            $st=$this->db->prepare($s);
            $res=$st->execute($d);
            if($res==false)
                return false;
            return true;
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
     * @param int $sessio Istunnon tunniste
     * @return array|boolean Joko löydetyt pelaajat tahi false
     * */
    public function tableFetch($start, $length, $order, $search, $sessio) {
        try {
            $ds = false;
            $tulos = array("lkm"=>0, "pelaajat"=>array(), "riveja"=>0, "filtered"=>0);
            $o="";
            $v="";
            if(isset($search["value"])) {
                $v = $search["value"];
                if(is_numeric($v))
                    $so = "and (nimi ~* :v or numero=:v)";
                else
                    $so = "and nimi ~* :v";
                $ds=true;
            }
            $d = array("length"=>$length, "start"=>$start, "sessio"=>$sessio);
            if($order !== false) {
                $s = "select numero, nimi from pelaaja where sessio=:sessio $so order by $order limit :length offset :start;";                
            } else {
                $s = "select numero, nimi from pelaaja where sessio=:sessio $so limit :length offset :start;";
            }
            if($ds)
                $d["v"]=$v;
            $st = $this->db->prepare($s);
            $res = $st->execute($d);
            if(!$res || $st->rowCount()==0)
                return $tulos;
            $pelaajat = $st->fetchAll();
            $s = "select count(*) as lkm from pelaaja where sessio=:sessio;";
            $st = $this->db->prepare($s);
            $res = $st->execute(array("sessio"=>$sessio));
            if(!$res || $st->rowCount()==0)
                return $tulos;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            $tulos["lkm"]=$row["lkm"];
            $tulos["pelaajat"]=$pelaajat;
            $tulos["riveja"]=count($pelaajat);
            $tulos["filtered"]=$row["lkm"];
            if($ds) {
                $s = "select count(*) as lkm from pelaaja where sessio=:sessio $so;";
                $st = $this->db->prepare($s);
                $res = $st->execute(array("v"=>$v, "sessio"=>$sessio));
                if($res && $st->rowCount()>0)
                    $tulos["filtered"]=$st->fetch()["lkm"];
            }
            return $tulos;
        }
        catch(PDOException $pe) {
            die($pe->getMessage());
        }
    }
}
?>