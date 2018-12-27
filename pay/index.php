<?php

unset($_SESSION['SesStudPay']);
session_start();
ini_set("display_errors", 1);

if (isset($_COOKIE['StudLang']) && ($_COOKIE['StudLang']==0 || $_COOKIE['StudLang']==1)){
   $_SESSION['SLG'] = $_COOKIE['StudLang'];
} else {
   $_SESSION['SLG'] = 0;
}

include_once '../lg.php';

if(!isset($_SESSION['SesStudPay']['Auth']) || $_SESSION['SesStudPay']['Auth']!==true) {
   if(isset($_POST['lgn']) && isset($_POST['pwd'])){

      $loginUs=trim($_POST['lgn']);
      $passUs=trim($_POST['pwd']);

      if(!empty($loginUs) && !empty($passUs)){
            if(!GetLU($loginUs,$passUs)){
               logn($loginUs,$lang['err1'][$_SESSION['SLG']],$lang);
               exit;
            } else {
               if(!GiveData($loginUs)){
                  logn($loginUs,$lang['err2'][$_SESSION['SLG']],$lang);
                  exit;
               } else {
                  GivePredmet();
                  header("location: view.php"); return;
               }
            }
      } else {
         logn($loginUs,$lang['err4'][$_SESSION['SLG']],$lang);
         exit;
      }
   } else {
      if(isset($_GET['closet']) && $_GET['closet']){
         logn('',$_GET['closet'],$lang);
         exit; 
      } else {
         logn('','',$lang);
         exit; 
      }
   }
} else {
  header("location: view.php"); return;
}


function GiveData($loginStud)
{
   include_once 'configStudent.php';
   $result = mssql_query("SELECT A.idStud, CONCAT(B.Name_F,' ',B.Name_I,' ',B.Name_O), B.Idf, B.IdKurs, B.IdGroup, C.Name, LEFT(C.Name,1), D.Name, D.NameEn, CONCAT(B.NameEng,' ',B.NameEngFirst) FROM dbo.logins A LEFT JOIN dbo.Student B ON B.IdStud=A.idStud LEFT JOIN dbo.Groups C ON C.IdGroup=B.IdGroup LEFT JOIN dbo.Facultets D ON D.Idf=B.Idf WHERE A.login='".$loginStud."'",$dbStud);

   if(mssql_num_rows($result)>=1){
      $arr=mssql_fetch_row($result);
      $_SESSION['SesStudPay']['idS']=$arr[0]; // ид Студента                 
      $_SESSION['SesStudPay']['nameS'][0]=$arr[1]; // ФИО Студента
      $_SESSION['SesStudPay']['nameS'][1]=$arr[9]; // ФИО Студента по-английски
      $_SESSION['SesStudPay']['idFakS']=$arr[2]; // ид факультета
      $_SESSION['SesStudPay']['kursS']=$arr[3]; // номер курса
      $_SESSION['SesStudPay']['idGroupS']=$arr[4]; // ид группы
      $_SESSION['SesStudPay']['gnameS']=$arr[5]; // номер группы
      $_SESSION['SesStudPay']['gfS']=$arr[6]; // первый номер из названия группы принадлежащий к номеру факультета (1 - лечфак, 2 - педфак...)
      $_SESSION['SesStudPay']['fnameS'][0]=$arr[7]; // название факультета
      $_SESSION['SesStudPay']['fnameS'][1]=$arr[8]; // название факультета по-английски

      unset($arr);
      mssql_free_result($result);
      return true;      
   } else {
      return false;
   }
}

function GivePredmet()
{
   include_once 'configMain.php';
   include_once 'config.php';
   $res = mysqli_query($dbMain, "SELECT idLessons, SUM(n21) FROM aerostat WHERE n21>=1 AND idStud=".$_SESSION['SesStudPay']['idS']." AND idFak=".$_SESSION['SesStudPay']['idFakS']." GROUP BY idStud,idLessons");

   if (mysqli_num_rows($res)>=1) {
      $iPredmet = 0;
      while ($arr = mysqli_fetch_row($res)) {
         $ress = mysqli_query($dbMain, "SELECT B.name, IF(CHAR_LENGTH(B.name)>70,CONCAT(LEFT(B.name, 67),'...'),B.name), B.nameEn, IF(CHAR_LENGTH(B.nameEn)>70,CONCAT(LEFT(B.nameEn, 67),'...'),B.nameEn) FROM lessons B WHERE B.id=".$arr[0]);
         // $ress = mysqli_query($dbMain, "SELECT n21 FROM aerostat WHERE idLessons=".$arr[0]." AND idStud=".$_SESSION['SesStudPay']['idS']);
         $les = mysqli_fetch_row($ress);
         $_SESSION['SesStudPay']['Predmet'][$iPredmet]=Array($arr[0], $les[0], $les[1], $arr[1], $les[2], $les[3]);
         $iPredmet++;
      }
      unset($arr, $les);
      mysqli_free_result($res);
      mysqli_free_result($ress);

   } else {
         $_SESSION['SesStudPay']['Predmet'][0]="";
   }
      $_SESSION['SesStudPay']['Auth']=true;

}


function GetLU($user,$pass){
   include_once 'configLdap.php';
   $ad = ldap_connect($opLdap['host'],$opLdap['port']);
   ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
   ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

   if($ad){
      $bd=@ldap_bind($ad,$user.$opLdap['dom'],$pass);
      if(!$bd){
         return false;         
      } else {
         return true;
      }
   } else {
      return false;         
   }
   ldap_close($ad);
}

/* ---------------------------------------------------------------------------------------------------------------------------------------------------- */


function logn($lgn='',$msg='',$lang){
?>

    <!doctype html>
    <html>
    <head>
        <TITLE><?php echo $lang['title'][$_SESSION['SLG']]; ?></TITLE>
        <meta charset="windows-1251">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="../style.css">
        <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
        <script src="../scripts/jquery-3.2.1.min.js"></script>
        <script src="../scripts/cookie.js"></script>
        <script src="../scripts/lang.js"></script>
<script>
<!--
function sbmt(){
   if(!flgn.lgn.value || !flgn.pwd.value) {
      alert("<?php echo $lang['err5'][$_SESSION['SLG']]; ?>");
   } else {
      document.flgn.submit();
   }
}
//-->
</script>
    </head>
<BODY>
<div class='Lang'><div class='ruen'>Ru</div><input type='checkbox' id='switch' /><label for='switch'></label><div class='ruen'>En</div></div>
<div class='LogF'>
<div class='LogForm'><h1><?php echo $lang['title'][$_SESSION['SLG']]; ?></h1>
<div class='LogFormMsg'><?php echo $msg; ?></div>

<form method="post" name="flgn" action="index.php" onsubmit="sbmt(); return false;">
<div class='Login'><?php echo $lang['login'][$_SESSION['SLG']]; ?><br></div>
<input type=text name="lgn" size="40" value="<?php echo $lgn; ?>">                     
<div class='Password'><?php echo $lang['pwd'][$_SESSION['SLG']]; ?><br></div>
<input type=password name="pwd" size="40"><br><br>
<input type=submit value="<?php echo $lang['enter'][$_SESSION['SLG']]; ?>" class="but">
</form>

</div>
</div>
</BODY>
</HTML>
<?php

}

?>