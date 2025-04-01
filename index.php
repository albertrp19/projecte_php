<?php
    require_once('./php/functions.php');
    session_start();
    
    if(checkSession()){
        header('Location: ./php/home.php');
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
            header('Location: ./php/home.php');
        }
        else{
            //!Si no, que avise del fallo sin especificar
            //header('Location ./html/registre.html');
            echo "No es possible iniciar sessió amb les dades";
        }
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inici de sessio</title>
</head>
<body>
    
    <form action="./index.php" method="post">
        <label for="user">Usuari o correu</label>
        <input type="text" name="user">

        <label for="passwd"> Contrasenya</label>
        <input type="password" name="passwd">

        <input type="submit" value="Enviar">
    </form>
    <a href="./php/registre.php">No tens compte? Registrar-se</a>

    <a href="">Has oblidat la teva contrasenya?</a>
</body>
</html>