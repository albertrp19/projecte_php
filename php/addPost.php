<?php
    
    require_once('../php/functions.php');
    session_start();
    if(!checkSession()){
        header('Location: ../index.html');
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
       
        $content = $_POST['content'];
        $infoUser = getProfileInfo($_SESSION['user'], $db);
        $userId = $infoUser['iduser'];
        if(isset($_POST['file'])){
            $file = $_POST['file'];
        }
        else{
            $file = null;
        }
        addPost($content, $userId, $file, $db);

        
        header('Location: ../html/home.php');
    }else{
        header('Location: ../html/home.php');
    }


?>