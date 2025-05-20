<?php
    require_once('../php/functions.php');
    session_start();
    if(!checkSession()){
        header('Location: ../index.html');
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //print_r($_FILES);
        $content = $_POST['content'];
        $infoUser = getProfileInfo($_SESSION['user'], $db);
        $userId = $infoUser['iduser'];
       
        addPost($content, $userId, $_FILES, $db);
        
        header('Location: ../html/home.php');
    }else{
        header('Location: ../html/home.php');
    }


?>