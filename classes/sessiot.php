<?php
/**
 * Sessiot
 *
 * @package SLS-Prototracker
 * @license http://opensource.org/licenses/GPL-2.0
 * @author Mauri "mos" Sahlberg
 *
 * */

 /**
  * Sessioiden rajapinta
  *
  * Sessioiden tallettaminen, etsiminen, muuttaminen ja poistaminen.
  * */
class SESSIOT {
  /** @var handle Database handle **/
    private $db;
    /** @var object SLS Database **/
    private $dbc;
    
    /**
     * Constructor
     * @param object $db Database-handle
     * */
    public function __construct($db) {
        $this->db = $db->getHandle();
        $this->dbc = $db;
        
    }
    
    /**
     * Lisäys
     * @param array $d Lisättävät tiedot
     * @return false|int palauttaa false jos meni käteen ja session tunnuksen jos ei
     * */
    public function addSession($d) {
        try {
            $s1 = "insert into sessio (";
            $s2 = " values (";
            $first=true;
            foreach($d as $k=>$v) {
                if($first) {
                    $s1.="$k";
                    $first=false;
                    $s2.=":$k";
                    continue;
                }
                $s1.=",$k";
                $s2.=",:$k";
            }
            $s=$s1.")".$s2.") returning id;";
            $st = $this->db->prepare($s);
            $r = $st->execute($d);
            if($r===false)
                return false;
            $row = $st->fetch(PDO::FETCH_ASSOC);
            return $row["id"];
            
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * Session talletus
     * @param int $id Session tunniste
     * @param array $d Lopppu istuntodata
     * @return boolean|int false jos meni räpylään ja tunnisteen jos onnistui
     * */
    public function saveSession($id, $d) {
        try {
            $s1="update sessio set ";
            $s2=" where id=:id;";
             $first=true;
            foreach($d as $k=>$v) {
                if($first) {
                    $s1.="$k=:$k";
                    $first=false;
                    continue;
                }
                $s1.=",$k=:$k";
            }
            $st = $this->db->prepare($s1.$s2);
            $d['id']=$id;
            $res = $st->execute($d);
            if($res===false)
                return false;
            return $id;
        }
        catch(PDOException $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * Pääsivun sessionouto
     *
     * Datatablesin tarvitsema sessiohaku. Käyttöoikeuksia varten lisätty parametri, joka
     * kertoo kuka hakeva käyttäjä on. @todo Toteuta käyttöoikeudet!
     * @param int $start Rivi, jolta nouto aloitetaan
     * @param int $length Montako riviä ladataan
     * @param string $order Kenttä, jonka mukaan sortataan.
     * @param string $search Etsitäänkö jotakin? Datatablesin filtteri
     * @param string $kuka Hakijan käyttäjätunnus
     * @return mixed Palautetaan false, mikäli haku itsessään epäonnistuu ja array rivejä muodossa [rivinumero][solunimi]=solun arvo
     * @todo Käyttöoikeudet!
     * */
    public function tableFetch($start, $length, $order, $search, $kuka) {
        try {
            $ds = false;
            $tulos = array("lkm"=>0, "sessiot"=>array(), "riveja"=>0, "filtered"=>0);
            $so="";
            $v="";
            if(isset($search["value"]) && $search["value"]!="") {
                $v = $search["value"];
                $so = "where (nimi ~* :v or luoja ~* :v or status ~* :v)";
                $ds = true;
            }
            if($order !== false) 
                $oclause="order by $order";
            else
                $oclause="";
                
            $s = "select * from sEtusivu $so $oclause limit :length offset :start";
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
            $sessiot = $st->fetchAll();
            $s = "select count(*) as lkm from sEtusivu;";
            $st = $this->db->prepare($s);
            $res = $st->execute();
            if(!$res || $st->rowCount()==0) {
                return $tulos;
            }
            $row = $st->fetch();
            $tulos["lkm"]=$row["lkm"];
            $tulos["sessiot"]=$sessiot;
            $tulos["riveja"]=count($sessiot);
            $tulos["filtered"]=$row["lkm"];
            if($ds) {
                $s = "select count(*) as lkm from sEtusivu $so;";
                $st = $this->db->prepare($s);
                $res = $st->execute(array("v"=>$v));
                if($res && $st->rowCount()>0) {
		    $row=$st->fetch(PDO::FETCH_ASSOC);
                    $tulos["filtered"]=$row["lkm"];
                }
            }
            return $tulos; 
        }
        catch(PDOException $e) {
            die($e->getMessage());
        }
    }
    
    /**
     * Session lataaminen
     * @param int $sessioid
     * @param str $kuka
     * @return mixed False, jos epäonnistui ja array jos onnistui
     * @todo Käyttöoikeudet!
     * */
    public function lataaSessio($sessioid, $kuka) {
        try {
            $s = "select * from sessio where id=:sessioid;";
            $st = $this->db->prepare($s);
            $res = $st->execute(array("sessioid"=>$sessioid));
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
