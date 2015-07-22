<?php
foreach($_SESSION as $k=>$v) {
    if(preg_match('/^(s_.*)|(p_.*)/',$k)) {
        unset($_SESSION[$k]);
    }
}
?>