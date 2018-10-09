<?php

$_SESSION['SesStud']['Auth']=false;                  
unset($_SESSION['SesStud']);

    session_start();
    session_unset();
    session_destroy();


    setcookie('login', '', 0, "/");
    setcookie('password', '', 0, "/");
    header('Location: index.php');
    exit;

?>
