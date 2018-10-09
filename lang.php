<?php
  session_start();
  if(isset($_GET['SLang']) && ($_GET['SLang']==0 || $_GET['SLang']==1)){
     setcookie('StudLang',$_GET['SLang'],time()+518400);
     $_SESSION['SLG'] = $_GET['SLang'];
//     header("Refresh:1");
  }
?>