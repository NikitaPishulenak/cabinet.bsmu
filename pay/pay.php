<?php 
ini_set("display_errors", 1);

if(isset($_GET['menuactiv'])){
    switch($_GET['menuactiv']) {
      case "generatePay":
            generatePay();
            break;

        case "completedPay":
            completedPay();
            break;

        case "deletePay":
            deletedPay();
            break;
    }
}


function generatePay(){
   if( isset($_GET['idOrder']) && is_numeric($_GET['idOrder']) && isset($_GET['idLessons']) && is_numeric($_GET['idLessons']) && isset($_GET['idStudent']) && is_numeric($_GET['idStudent']) && isset($_GET['status']) && is_numeric($_GET['status']) && isset($_GET['price']) && is_numeric($_GET['price']) && isset($_GET['idZap'])){
      include_once 'configMain.php';
         $res = mysqli_query($dbMain, "INSERT INTO payments (idOrder, idLessons, idStudent, status, price, generateDT, idZapis) VALUES ('".$_GET['idOrder']."', '".$_GET['idLessons']."', '".$_GET['idStudent']."', '".$_GET['status']."', '".$_GET['price']."', CURRENT_TIMESTAMP(), '".$_GET['idZap']."')");

         $idZapises=explode("|", $_GET['idZap']);
         foreach ($idZapises as $zap){
            mysqli_query($dbMain, "UPDATE rating SET pStatus=1 WHERE id=".$zap);
         }
         if($res) echo "added";
   }   
}

function completedPay(){
   if( isset($_GET['idOrder']) && is_numeric($_GET['idOrder'])){
      include_once 'configMain.php';
      $res = mysqli_query($dbMain, "UPDATE payments SET status=3, payDT=CURRENT_TIMESTAMP() WHERE idOrder=".$_GET['idOrder']);
      $res= mysqli_query($dbMain, "SELECT idZapis FROM payments WHERE idOrder=".$_GET['idOrder']." and idZapis!=0 LIMIT 1");
      list($zapis) = mysqli_fetch_row($res);
      $idZapises=explode("|", $zapis);
      foreach ($idZapises as $zap){
         mysqli_query($dbMain, "UPDATE rating SET pStatus=2 WHERE id=".$zap);
      }
   }
}

function deletedPay(){
   if( isset($_GET['idOrder']) && is_numeric($_GET['idOrder'])){
      include_once 'configMain.php';
      $res = mysqli_query($dbMain, "UPDATE payments SET status=4, payDT=CURRENT_TIMESTAMP() WHERE idOrder=".$_GET['idOrder']." and status=2");
      $res= mysqli_query($dbMain, "SELECT idZapis FROM payments WHERE idOrder=".$_GET['idOrder']." and idZapis!=0 LIMIT 1");
      list($zapis) = mysqli_fetch_row($res);
      $idZapises=explode("|", $zapis);
      foreach ($idZapises as $zap){
         mysqli_query($dbMain, "UPDATE rating SET pStatus=0 WHERE id=".$zap);
      }
   }
}

?>