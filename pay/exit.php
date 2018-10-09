<?php

$_SESSION['SesStudPay']['Auth']=false;                  
unset($_SESSION['SesStudPay']);

    session_start();
    session_unset();
    session_destroy();


    setcookie('login', '', 0, "/");
    setcookie('password', '', 0, "/");
    header('Location: index.php');
    exit;

?>
