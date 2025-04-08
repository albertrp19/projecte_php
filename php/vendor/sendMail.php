<?php
session_start();
use  PHPMailer\PHPMailer\PHPMailer;
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  require  'vendor/autoload.php';
  
  $attatchment = $_FILES["file"];
  $tmp_name = $attatchment["tmp_name"];
  $filenames = $attatchment["name"];
  $att_dir = "archivos/";

  require  'vendor/autoload.php';
  $mail  =  new  PHPMailer();
  $mail->IsSMTP();
  //Configuració  del  servidor  de  Correu
  //Modificar  a  0  per  eliminar  msg  error
  $mail->SMTPDebug  =  0;
  $mail->SMTPAuth  =  true;
  $mail->SMTPSecure  =  'tls';
  $mail->Host  =  'smtp.gmail.com';
  $mail->Port  =  587;

    //Credencials  del  compte  GMAIL
    $mail->Username  =  $_SESSION['email'];
    $mail->Password  =  $_SESSION['passwd'];

  //Dades del correu electrònic
    $mail->SetFrom($_SESSION['email'],'Abel Sierra');
    $mail->Subject= $_POST['asunto'];
    $mail->MsgHTML($_POST['cuerpo']);

    if (!empty($tmp_name)) {
      $file_path = $att_dir . basename($filenames);
      if (move_uploaded_file($tmp_name, $file_path)){
        $mail->addAttachment($file_path);
      }
    }
    //Destinatari
    $address= $_POST['destinatario'];
    $mail->AddAddress($address,'Test');
    //Enviament
    
    $result=$mail->Send();
    if(!$result){
      echo'Error:'.$mail->ErrorInfo;
    }else{
      echo "Correu enviat";
     
    }
}
?>