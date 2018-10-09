<?php

unset($_SESSION['SesStud']);
session_start();
//ini_set("display_errors", 1);

if (isset($_COOKIE['StudLang']) && ($_COOKIE['StudLang']==0 || $_COOKIE['StudLang']==1)){
   $_SESSION['SLG'] = $_COOKIE['StudLang'];
} else {
   $_SESSION['SLG'] = 0;
}


include_once 'lg.php';

if(!isset($_SESSION['SesStud']['Auth']) || $_SESSION['SesStud']['Auth']!==true) {
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
                  if(!GivePredmet()){
                     logn($loginUs,$lang['err3'][$_SESSION['SLG']],$lang);
                     exit;
                  } else {
                     header("location: view.php"); return;
                  }
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
      $_SESSION['SesStud']['idS']=$arr[0]; // ид Студента                 
      $_SESSION['SesStud']['nameS'][0]=$arr[1]; // ФИО Студента
      $_SESSION['SesStud']['nameS'][1]=$arr[9]; // ФИО Студента по-английски
      $_SESSION['SesStud']['idFakS']=$arr[2]; // ид факультета
      $_SESSION['SesStud']['kursS']=$arr[3]; // номер курса
      $_SESSION['SesStud']['idGroupS']=$arr[4]; // ид группы
      $_SESSION['SesStud']['gnameS']=$arr[5]; // номер группы
      $_SESSION['SesStud']['gfS']=$arr[6]; // первый номер из названия группы принадлежащий к номеру факультета (1 - лечфак, 2 - педфак...)
      $_SESSION['SesStud']['fnameS'][0]=$arr[7]; // название факультета
      $_SESSION['SesStud']['fnameS'][1]=$arr[8]; // название факультета по-английски

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

   $res = mysqli_query($dbMain, "SELECT A.id_lesson, B.name, IF(CHAR_LENGTH(B.name)>70,CONCAT(LEFT(B.name, 67),'...'),B.name), B.nameEn, IF(CHAR_LENGTH(B.nameEn)>70,CONCAT(LEFT(B.nameEn, 67),'...'),B.nameEn) FROM schedule A LEFT JOIN lessons B ON B.id=A.id_lesson WHERE (A.course=".$_SESSION['SesStud']['kursS']." AND A.id_faculty=".$_SESSION['SesStud']['idFakS'].") OR (A.course=".$_SESSION['SesStud']['kursS']." AND A.id_faculty=".$fac[$_SESSION['SesStud']['gfS']].") GROUP BY A.id_lesson ORDER BY B.name");

   if (mysqli_num_rows($res)>=1) {
      $iPredmet = 0;
      while ($arr = mysqli_fetch_row($res)) {
         $ress = mysqli_query($dbMain, "SELECT SUM(n10+n11+n12+n13+n14+n15+n16+n17+n18+n19+n23+n24+n26+n27+n31+n32+n33+n34+n35+n36+n37+n40+n41+n42+n43+n44+n45) FROM aerostat WHERE idLessons=".$arr[0]." AND idStud=".$_SESSION['SesStud']['idS']." AND idFak=".$_SESSION['SesStud']['idFakS']." GROUP BY idStud");
         list($cntOc) = mysqli_fetch_row($ress);
         $_SESSION['SesStud']['Predmet'][$iPredmet]=Array($arr[0],$arr[1],$arr[2],$cntOc,$arr[3],$arr[4]);
         $iPredmet++;
      }
      unset($arr, $cntOc);
      mysqli_free_result($res);
      mysqli_free_result($ress);

      $_SESSION['SesStud']['Auth']=true;

      return true;
   } else {
      return false;
   }
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


function logn($lgn='',$msg='', $lang){
?>

    <!doctype html>
    <html>
    <head>
        <TITLE><?php echo $lang['title'][$_SESSION['SLG']]; ?></TITLE>
        <meta charset="windows-1251">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="style.css">
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