<?php
    require_once(__DIR__ . '/connexioDB.php');
  
    function checkSession(){
        return (isset($_SESSION['user']));
    
    }

    function existsUser($username, $postUser, $postMail){
        return (($username['username'] == $postUser || $username['mail'] == $postMail));

    }

    function checkUser($username, $postUser, ){
        return (($username['username'] == $postUser || $username['mail'] == $postUser));
    }

    function checkPasswd( $username,$postPass){
        return  password_verify($postPass, $username['passHash']);
    }

    function checkActive($username){
        return $username['active'] == 1;
    }

    function setSession($username){
        $_SESSION['user'] = $username['username'];
        $_SESSION['nom'] = $username['userFirstName'];
        $_SESSION['cognom'] = $username['userLastName'];
        setcookie("username", $username['username'], time() + (7 * 24 * 60 * 60), "/");
        ultimAcces($username['lastSignIn']);
    }

    function ultimAcces($lastSignIn){
        setcookie("ultima_data",$lastSignIn, time() + (7 * 24 * 60 * 60), "/"); 
        
    }

    function updateLastSignIn($user, $db){
        $sql = 'UPDATE users SET lastSignIn = ? WHERE username = ?';
        $preparada = $db->prepare($sql);
        $preparada->execute(array(date("Y-m-d H:i:s"),$user));
    }

    function closeSession(){
        session_unset(); // Esborra totes les variables de sessió
        session_destroy(); // Destrueix la sessió
        setcookie("ultima_data","", time() - (7 * 24 * 60 * 60), "/");
        setcookie("username", "", time() - (7 * 24 * 60 * 60), "/");
        header("Location: ../index.php");
    }


?>