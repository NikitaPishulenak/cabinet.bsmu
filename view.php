<?php
unset($_SESSION['SesStud']);
session_start();

ini_set("display_errors", 1);

if (isset($_COOKIE['StudLang']) && ($_COOKIE['StudLang']==0 || $_COOKIE['StudLang']==1)){
   $_SESSION['SLG'] = $_COOKIE['StudLang'];
} else {
   $_SESSION['SLG'] = 0;
}

include_once 'lg.php';

if (!isset($_SESSION['SesStud']['Auth']) || $_SESSION['SesStud']['Auth'] !== true) {
   if(isset($_GET['ajaxTrue']) && $_GET['ajaxTrue']){
    echo "<div class='Not'>".$lang['err6'][$_SESSION['SLG']]."</div>";
    exit;
   } else {
      header('Location: index.php?closet='.$lang['err7'][$_SESSION['SLG']]);
      exit;
   }
}


if(isset($_GET['idSubject']) && is_numeric($_GET['idSubject']) && isset($_GET['idStudent']) && is_numeric($_GET['idStudent'])){
   $_GET['idSubject'] = substr($_GET['idSubject'],0,4);
   $_GET['idStudent'] = substr($_GET['idStudent'],0,6);
   GroupViewP($_GET['idSubject'], $_GET['idStudent'], $lang);
}else{
   MainF($lang);
}


function GroupViewP($idSu, $idSt, $lang){
   include_once 'configMain.php';

   $resultS = mysqli_query($dbMain, "SELECT DATE_FORMAT(B.LDate,'%e.%m.%Y'), A.RatingO, A.PL, A.PKE, B.nLesson, A.pStatus, A.Nn FROM rating A LEFT JOIN lesson B ON (B.id=A.idLesson) WHERE A.idStud=".$idSt." AND A.idLessons=".$idSu." AND A.del=0 ORDER BY A.PL,B.LDate");
   if(mysqli_num_rows($resultS)>=1){
      $retVal="";
      $trueP=0; $trueL=0;
      while($arrSS = mysqli_fetch_row($resultS)){

         if(!$arrSS[2] && !$trueP){
            $trueP = 1;
            $retVal.="<div class='titleO'>".$lang['GVP1'][$_SESSION['SLG']]."</div>\n";
         } else if ($arrSS[2] && !$trueL){
            $trueL = 1;
            $retVal.="<div class='clr'></div><div class='titleO'>".$lang['GVP2'][$_SESSION['SLG']]."</div>\n";
         }
         switch($arrSS[3]){
            case 1:
               $retVal.="<div class='Oc Koll' title='".$lang['GVP3'][$_SESSION['SLG']]."'><div class='DataO'><div class='nLesson'>".$arrSS[4]."</div>".$arrSS[0]."</div><div class='Otmetka' data-Nn=".$arrSS[6].">".$arrSS[1]."</div></div>\n";   
               break;
            case 2:
               $retVal.="<div class='Oc Exm' title='".$lang['GVP4'][$_SESSION['SLG']]."'><div class='DataO'><div class='nLesson'>".$arrSS[4]."</div>".$arrSS[0]."</div><div class='Otmetka' data-Nn=".$arrSS[6].">".$arrSS[1]."</div></div>\n";
               break;
            default:
               $retVal.="<div class='Oc'><div class='DataO'><div class='nLesson'>".$arrSS[4]."</div>".$arrSS[0]."</div><div class='Otmetka' data-Nn=".$arrSS[6].">".$arrSS[1]."</div></div>\n";
               break;
         }
 
      }
      mysqli_free_result($resultS);
      echo $retVal;
      unset($retVal);
   } else {
      echo "<div class='Not'>".$lang['err8'][$_SESSION['SLG']]."</div>";
   }
}


//----------------------------------------------------------------------------------------------


function MainF($lang){
    $retVal="<p>".$lang['GVP5'][$_SESSION['SLG']]." <strong>".$_SESSION['SesStud']['gnameS']."</strong> (".$_SESSION['SesStud']['kursS'].$lang['GVP6'][$_SESSION['SLG']].")<br>".$_SESSION['SesStud']['fnameS'][$_SESSION['SLG']]."</p><hr>";

    $countPredmet=count($_SESSION['SesStud']['Predmet']);
    $retVal.="\n<div class='DialogP'><div class='titleBox'><H2>".$lang['GVP7'][$_SESSION['SLG']]."&nbsp;&nbsp;/&nbsp;&nbsp;<a href='/pay/' target='_blank'>".$lang['GVP8'][$_SESSION['SLG']]."</a></H2></div>\n";

    include_once 'configStudent.php';

    for($ii=0; $ii<=($countPredmet-1); $ii++){
        $retVal.="<div class='DialogFakFak' data-idSubject='".$_SESSION['SesStud']['Predmet'][$ii][0]."'>\n<span class='shortText'>".$_SESSION['SesStud']['Predmet'][$ii][$lang['BDL'][$_SESSION['SLG']]+1]."</span>\n<span class='fullText'>".$_SESSION['SesStud']['Predmet'][$ii][$lang['BDL'][$_SESSION['SLG']]]."</span>&nbsp;<span class='fullTextClose' title='".$lang['GVP9'][$_SESSION['SLG']]."'>X</span>\n<div class='content_grade'></div>\n".($_SESSION['SesStud']['Predmet'][$ii][3] ? "<div class='COAll' title='".$lang['GVP10'][$_SESSION['SLG']]." ".$_SESSION['SesStud']['Predmet'][$ii][3]."'>".$_SESSION['SesStud']['Predmet'][$ii][3]."</div>\n" : "")."</div>\n";
    }
    $retVal.="</div>\n";
    echo HeaderFooter($retVal, $verC, $verS, $lang);
}


//----------------------------------------------------------------------------------------------


function HeaderFooter($content,$vC='',$vS='',$lang){
    ?>
    <!doctype html>
    <html>
    <head>
        <title><?php echo $_SESSION['SesStud']['nameS'][$_SESSION['SLG']]; ?></title>
        <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <meta charset="windows-1251">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="style.css<?php echo $vC; ?>">
        <script src="scripts/jquery-3.2.1.min.js"></script>
        <script src="scripts/demoscript.js<?php echo $vS; ?>"></script>
      <script src="scripts/cookie.js"></script>
        <script src="scripts/lang.js<?php echo $vS; ?>"></script>
    </head>
    <body>
    <?php echo LevelView($lang); ?>
    <input type='hidden' id='idStudent' value='<?php echo $_SESSION['SesStud']['idS']; ?>'>
    <div class="Header"><H2><?php echo $_SESSION['SesStud']['nameS'][$_SESSION['SLG']]; ?></H2></div>
    <?php echo $content; ?>
    <div style="clear:both; margin-bottom:20px;">&nbsp;</div>
    <div class="support"><?php echo $lang['GVP11'][$_SESSION['SLG']]; ?><br><br>
        <div class="note">
            <?php echo $lang['GVP12'][$_SESSION['SLG']]; ?><br>
            <?php echo $lang['GVP13'][$_SESSION['SLG']]; ?><br>
            <?php echo $lang['GVP14'][$_SESSION['SLG']]; ?><br>
            <?php echo $lang['GVP15'][$_SESSION['SLG']]; ?>
        </div>
    </div>
    <div style="clear:both; margin-bottom:50px;">&nbsp;</div>
    </body>
    </html>
    <?php
}

function LevelView($lang){
        return "
<div class='Lang'><div class='ruen'>Ru</div><input type='checkbox' id='switch' /><label for='switch'></label><div class='ruen'>En</div></div>
<div class='Exit'>
<div class='Kvadrat OO'></div><div>".$lang['GVP16'][$_SESSION['SLG']]."</div><div class='C'></div>
<div class='Kvadrat OK'></div><div>".$lang['GVP17'][$_SESSION['SLG']]."</div><div class='C'></div>
<div class='Kvadrat OE'></div><div>".$lang['GVP18'][$_SESSION['SLG']]."</div><div class='C'></div>
<a href='exit.php'><H2>".$lang['GVP19'][$_SESSION['SLG']]."</H2></a>
</div>
<div class='C'></div>";
}

?>
