<?php
    require_once('./functions.php');
    session_start();
    
    if(checkSession()){
        header('Location: ../html/home.php');
    }
  
    if($_SERVER['REQUEST_METHOD'] == 'POST'){ 

        $sql ="SELECT * FROM users";

        $select = $db->query($sql);

        $trobat= FALSE;

        foreach($select as $username){

            if(checkUser($username, $_POST['user']) && 
               checkActive($username) && 
               checkPasswd($username, $_POST['passwd'])){
                //!Entra si el usuario/mail existe en la bbdd coincide passwd y esta active 
                
                setSession($username);
                $trobat = TRUE;  
                
            }
        }
        if($trobat){
            //!Si lo encuentra, entra al home
            //! Actualiza el ultimo login, la funcion necesita pasar la variable DB por parametro porque no es global
            updateLastSignIn($_POST['user'], $db);
            header('Location: ../html/home.php');
        }
        else{
            //!Si no, que avise del fallo sin especificar 
            echo "No es possible iniciar sessió amb les dades";
        }
        }
?>