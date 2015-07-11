<?php
/**
 * Sessiot
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
}
?>