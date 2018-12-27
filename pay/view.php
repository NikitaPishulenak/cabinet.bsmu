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

if (!isset($_SESSION['SesStudPay']['Auth']) || $_SESSION['SesStudPay']['Auth'] !== true) {
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
}else if (@$_GET['history']) {
  showHistory($lang);
}
else{
   MainF($lang);
}


function GroupViewP($idSu, $idSt, $lang){
   include_once 'configMain.php';

   $resultS = mysqli_query($dbMain, "SELECT DATE_FORMAT(B.LDate,'%e.%m.%Y'), A.RatingO, A.PL, A.PKE, A.id, A.pStatus FROM rating A LEFT JOIN lesson B ON (B.id=A.idLesson) WHERE A.idStud=".$idSt." AND A.idLessons=".$idSu." AND A.del=0 AND (A.RatingO LIKE '21%' OR A.RatingO LIKE '__21%' OR A.RatingO LIKE '____21%') ORDER BY A.PL,B.LDate");
   
   if(mysqli_num_rows($resultS)>=1){
      $retVal="<br>";
      $trueP=0; $trueL=0;
      while($arrSS = mysqli_fetch_row($resultS)){

         if(!$arrSS[2] && !$trueP){
            $trueP = 1;
            $retVal.="<div class='titleO'>".$lang['GVP1'][$_SESSION['SLG']]."</div>\n";
         } else if ($arrSS[2] && !$trueL){
            $trueL = 1;
            $retVal.="<div class='clr'></div><div class='titleO'>".$lang['GVP2'][$_SESSION['SLG']]."</div>\n";
         }
         else if ($trueP==0 && $trueL==0) {
            $retVal="";

         }

          $logGr = mysqli_query($dbMain, "SELECT RatingO FROM logi WHERE idRating =".$arrSS[4]." AND (RatingO NOT LIKE '20%' AND RatingO NOT LIKE '__20%' AND RatingO NOT LIKE '____20%' AND RatingO NOT LIKE '21%' AND RatingO NOT LIKE '__21%' AND RatingO NOT LIKE '____21%' AND RatingO NOT LIKE '22%' AND RatingO NOT LIKE '__22%' AND RatingO NOT LIKE '____22%') ORDER BY DateO DESC, TimeO DESC LIMIT 1");
          $curGr=mysqli_fetch_row($logGr);
          mysqli_free_result($logGr);
          switch($arrSS[3]){
            case 1:
               $retVal.="<div class='Oc Koll' title='".$lang['GVP3'][$_SESSION['SLG']]."' data-Zapis='".$arrSS[4]."' data-PL='".$arrSS[2]."' data-pStatus='".$arrSS[5]."'><div class='DataO'>".$arrSS[0]."</div><div class='Otmetka'>".$curGr[0]."</div></div>\n";   
               break;
            case 2:
               $retVal.="<div class='Oc Exm' title='".$lang['GVP4'][$_SESSION['SLG']]."'  data-Zapis='".$arrSS[4]."' data-PL='".$arrSS[2]."' data-pStatus='".$arrSS[5]."'><div class='DataO'>".$arrSS[0]."</div><div class='Otmetka'>".$curGr[0]."</div></div>\n";
               break;
            default:
               $retVal.="<div class='Oc'  data-Zapis='".$arrSS[4]."' data-PL='".$arrSS[2]."' data-pStatus='".$arrSS[5]."'><div class='DataO'>".$arrSS[0]."</div><div class='Otmetka'>".$curGr[0]."</div></div>\n";
                break;
           }
         // } 
      }
      mysqli_free_result($resultS);

      echo $retVal."<div style='clear:both; margin-bottom:10px;'>&nbsp;</div><input type='button' value='".$lang['pay1'][$_SESSION['SLG']]."' id='pay'>";
      unset($retVal);
   } else {
      echo "<div class='Not'>".$lang['err9'][$_SESSION['SLG']]."</div>";
   }
}


//----------------------------------------------------------------------------------------------


function MainF($lang){
    $retVal="<p>".$lang['GVP5'][$_SESSION['SLG']]." <strong>".$_SESSION['SesStudPay']['gnameS']."</strong> (".$_SESSION['SesStudPay']['kursS'].$lang['GVP6'][$_SESSION['SLG']].")<br>".$_SESSION['SesStudPay']['fnameS'][$_SESSION['SLG']]."</p><hr>";

    $countPredmet=count($_SESSION['SesStudPay']['Predmet']);
    include_once 'configStudent.php';

    if($countPredmet==1 && empty($_SESSION['SesStudPay']['Predmet'][0])){
       $retVal.="\n<div class='DialogP'><div class='titleBox'><H2>".$lang['pay2'][$_SESSION['SLG']]."&nbsp;&nbsp;/&nbsp;&nbsp;<a href='?history=1'>".$lang['pay3'][$_SESSION['SLG']]."</a></H2></div></div>\n";
    } else {
       $retVal.="\n<div class='DialogP'><div class='titleBox'><H2>".$lang['pay4'][$_SESSION['SLG']]."&nbsp;&nbsp;/&nbsp;&nbsp;<a href='?history=1'>".$lang['pay3'][$_SESSION['SLG']]."</a></H2></div>\n";
       for($ii=0; $ii<=($countPredmet-1); $ii++){
          $retVal.="<div class='DialogFakFak' data-idSubject='".$_SESSION['SesStudPay']['Predmet'][$ii][0]."'>\n<span class='shortText'>".$_SESSION['SesStudPay']['Predmet'][$ii][$lang['BDL'][$_SESSION['SLG']]+1]."</span>\n<span class='fullText'>".$_SESSION['SesStudPay']['Predmet'][$ii][$lang['BDL'][$_SESSION['SLG']]]."</span>&nbsp;<span class='fullTextClose' title='".$lang['GVP9'][$_SESSION['SLG']]."'>X</span>\n<div class='content_grade'></div>\n".($_SESSION['SesStudPay']['Predmet'][$ii][3] ? "<div class='CO' title='".$lang['pay5'][$_SESSION['SLG']]." ".$_SESSION['SesStudPay']['Predmet'][$ii][3]."'>".$_SESSION['SesStudPay']['Predmet'][$ii][3]."</div>\n" : "")."</div>\n";
       }
       $retVal.="</div>\n";
   }

    echo HeaderFooter($retVal, $verC, $verS, $lang);
}


//----------------------------------------------------------------------------------------------

function showHistory($lang){
  include_once 'configStudent.php';
  ?>
    <!doctype html>
    <html>
    <head>
        <title><?php echo $_SESSION['SesStudPay']['nameS'][$_SESSION['SLG']]; ?></title>
        <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <meta charset="windows-1251">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="scripts/jquery-ui.css">
        <link rel="stylesheet" href="../style.css<?php echo $verC; ?>">
        <script src="scripts/jquery-3.2.1.min.js"></script>
        <script src="scripts/jquery-ui.js"></script>
        <script src="scripts/payscript.js<?php echo $verS; ?>"></script>
        <script src="../scripts/cookie.js"></script>        
        <script src="../scripts/lang.js<?php echo $verS; ?>"></script>

    </head>
    <body>
    <?php echo LevelView($lang); ?>
    <input type='hidden' id='idStudent' value='<?php echo $_SESSION['SesStudPay']['idS']; ?>'>
    <div class="Header"><H2><?php echo $_SESSION['SesStudPay']['nameS'][$_SESSION['SLG']]; ?></H2></div>
    <?php echo"<p>".$lang['GVP5'][$_SESSION['SLG']]." <strong>".$_SESSION['SesStudPay']['gnameS']."</strong> (".$_SESSION['SesStudPay']['kursS'].$lang['GVP6'][$_SESSION['SLG']].")<br>".$_SESSION['SesStudPay']['fnameS'][$_SESSION['SLG']]."</p><hr><div class='DialogP'><div class='titleBox'><H2><a href='/pay/'>".$lang['pay4'][$_SESSION['SLG']]."</a>&nbsp;&nbsp;/&nbsp;&nbsp;".$lang['pay3'][$_SESSION['SLG']]."</H2></div></div>\n";

      include_once 'configMain.php';
      $result = mysqli_query($dbMain, "SELECT id, idOrder, idLessons, price, DATE_FORMAT(generateDT,'%d.%m.%Y'), DATE_FORMAT(payDT,'%d.%m.%Y'), idZapis, status FROM payments WHERE idStudent ='".$_SESSION['SesStudPay']['idS']."' AND (status=2 OR status=3) ORDER BY status ASC, generateDT DESC");
      if(mysqli_num_rows($result)>=1){
        echo "<div class='clr'></div><div class='clr'></div><div class='headerPayList'><div class='gen_les'></div><div class='gen_idOrder'>".$lang['pay7'][$_SESSION['SLG']]."</div><div class='gen_price'>".$lang['pay8'][$_SESSION['SLG']]."</div><div class='gen_dt'>".$lang['pay9'][$_SESSION['SLG']]."</div><div class='pay_dt'>".$lang['pay10'][$_SESSION['SLG']]."</div><div class='gen_countAbs'>".$lang['pay11'][$_SESSION['SLG']]."</div></div>";
        $trueGen=0; $truePay=0; $retVal="";
        while($arrRes = mysqli_fetch_row($result)){
          $countAbs=explode("|", $arrRes[6]);
          if(($arrRes[7]==2) && ($trueGen==0)){
            $trueGen=1;
            echo"<div class='notPaid'><div><strong style='color:#760000'>".$lang['pay12'][$_SESSION['SLG']]."</strong></div><div class='clr'></div>";
          }else if(($arrRes[7]==3) && ($truePay==0)){
            $truePay=1;
            if($trueGen) echo "</div>";
            echo"<div class='paid'><div class='clr'></div><div class='clr'></div><div><strong>".$lang['pay13'][$_SESSION['SLG']]."</strong></div><div class='clr'></div>";
          }

            $resLes = mysqli_query($dbMain, "SELECT IF(CHAR_LENGTH(B.name)>50,CONCAT(LEFT(B.name, 47),'...'), B.name), IF(CHAR_LENGTH(B.nameEn)>50,CONCAT(LEFT(B.nameEn, 47),'...'), B.nameEn) FROM lessons B WHERE B.id=".$arrRes[2]);
            $les= mysqli_fetch_row($resLes);
            echo "<div class='genPay'><div class='gen_les' title='".$lang['GVP7'][$_SESSION['SLG']]."'>".$les[$_SESSION['SLG']]."</div><div class='gen_idOrder' title='".$lang['pay6'][$_SESSION['SLG']]."'><strong>".$arrRes[1]."</strong></div><div class='gen_price' title='".$lang['pay8'][$_SESSION['SLG']]."'>".$arrRes[3]." ".$lang['pay14'][$_SESSION['SLG']]."</div><div class='gen_dt' title='".$lang['pay15'][$_SESSION['SLG']]."'>".$arrRes[4]."</div><div class='pay_dt' title='".$lang['pay16'][$_SESSION['SLG']]."'>".(($arrRes[7]==3) ? $arrRes[5] : "")."</div><div class='gen_countAbs' title='".$lang['pay17'][$_SESSION['SLG']]."'>".count($countAbs)."</div><div class='cancelPay' title='".$lang['pay18'][$_SESSION['SLG']]."'>".(($arrRes[7]==3) ? "" : "<a href='#'data-idPay=".$arrRes[0].">".$lang['pay19'][$_SESSION['SLG']]."</a>")."</div></div>";
         
        }
        echo"<div style='clear:both; margin-bottom:20px;'>&nbsp;</div><div class='support'>".$lang['pay28'][$_SESSION['SLG']]."</div><div style='clear:both; margin-bottom:50px;'>&nbsp;</div></div>";

        ?>

        <form title='<?php echo $lang['pay27'][$_SESSION['SLG']]; ?>' id='confirm_delPay'>
              <span><?php echo $lang['pay21'][$_SESSION['SLG']]; ?></span> <br><br> <span><?php echo $lang['pay7'][$_SESSION['SLG']]; ?>:  <strong id="idDelPay"></strong></span><br>
              <span><?php echo $lang['pay8'][$_SESSION['SLG']]; ?>:  <strong id="idDelPrice"></strong></span><br>
                <br>                    
        </form>
      <?
      } else{
        echo "<h2>".$lang['pay20'][$_SESSION['SLG']]."</h2><div style='clear:both; margin-bottom:20px;'>&nbsp;</div><div class='support'>".$lang['pay28'][$_SESSION['SLG']]."</div><div style='clear:both; margin-bottom:50px;'>&nbsp;</div>";
      } 
      ?>

      </body>
    </html>
    <?php
   
}


//----------------------------------------------------------------------------------------------


function HeaderFooter($content,$vC='',$vS='', $lang){
    ?>
    <!doctype html>
    <html>
    <head>
        <title><?php echo $_SESSION['SesStudPay']['nameS'][$_SESSION['SLG']]; ?></title>
        <META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <meta charset="windows-1251">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="scripts/jquery-ui.css">
        <link rel="stylesheet" href="../style.css<?php echo $vC; ?>">
        <script src="scripts/jquery-3.2.1.min.js"></script>
        <script src="scripts/jquery-ui.js"></script>
        <script src="scripts/payscript.js<?php echo $vS; ?>"></script>
        <script src="../scripts/cookie.js"></script>        
        <script src="../scripts/lang.js<?php echo $vS; ?>"></script>
    </head>
    <body>
    <?php echo LevelView($lang); ?>
    <input type='hidden' id='idStudent' value='<?php echo $_SESSION['SesStudPay']['idS']; ?>'>
    <div class="Header"><H2><?php echo $_SESSION['SesStudPay']['nameS'][$_SESSION['SLG']]; ?></H2></div>
    <?php echo $content; ?>
    <div style="clear:both; margin-bottom:20px;">&nbsp;</div>
    <div class="support"><?php echo $lang['pay28'][$_SESSION['SLG']]; ?></div>
    <div style="clear:both; margin-bottom:50px;">&nbsp;</div>
    
    <div>
        <form title='<?php echo $lang['pay26'][$_SESSION['SLG']]; ?>' id='verifyDialog'>
              <span><?php echo $lang['pay22'][$_SESSION['SLG']]; ?> <strong id="sumPay"></strong> <?php echo $lang['pay23'][$_SESSION['SLG']]; ?></span>
                
                <br>                    
        </form>

        <form title='<?php echo $lang['pay6'][$_SESSION['SLG']]; ?>' id='payIdDialog'>
              <span><?php echo $lang['pay24'][$_SESSION['SLG']]; ?><br><br><strong id="idBepay"></strong></span><br><br>
              <span class='kursiv'><i><?php echo $lang['pay25'][$_SESSION['SLG']]; ?></i></span>            
                <br>                    
        </form>

    </div>
</div>

 
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
