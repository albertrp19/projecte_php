<?php
    require_once('./functions.php');
    session_start();
    if(checkSession()){
        header('Location: ./home.php');
    }

    $user_mail_exisit = FALSE;

    if ($_SERVER["REQUEST_METHOD"] === 'POST'){
        $mail = $_POST['email']; // Varchar(40) (Unique)
        $username = $_POST['username']; // Varchar(16) (Unique)
        
        //! HAY QUE HACER QUE LA CONTRASEÑA SEA FUERTE CON VALIDAICIONES
        $pass = $_POST['password']; // Varchar(60)
        
        if($pass === $_POST['verify_password']){ //verificar ambas contraseñas son iguales
            $firstName = $_POST['first_name'] ?? ''; // Varchar(60) (Opcional)
            $lastName = $_POST['last_name'] ?? ''; // Varchar(120) (Opcional)
    
            $sql = 'SELECT mail, username FROM users';
            $select = $db->query($sql);
        
            foreach ($select as $users) {
                if(existsUser($users,$username,$mail)){
                    $user_mail_exisit = TRUE;
                    break;
                }
            }
            if ($user_mail_exisit == FALSE) {

                $token_activacio = hash('sha256', uniqid(rand(), true));

                $sql_insert = 'INSERT INTO users(mail,username,passHash,userFirstName,userLastName,creationDate,removeDate,lastSignIn,activationDate,activationCode,resetPassExpiry,resetPassCode) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)';
                $preparada = $db->prepare($sql_insert);
                #falta agregar en el array activationdate, activation code, reset passExpiry resetPassCode
                $preparada->execute(array($mail,$username,password_hash($pass, PASSWORD_BCRYPT),$firstName,$lastName,date("Y-m-d H:i:s"),NULL,date("Y-m-d H:i:s"),NULL,$token_activacio,NULL,NULL));
                activeUser($token_activacio, $mail);
                echo "
                <script>
                    alert('Registre realitzat correctament! Accepta per continuar');
                    window.location.href = '../index.html'; // Redirigir después del mensaje
                </script>";
                exit();
                
            }else {
                echo "L'usuari/mail ja estan registrat";
            }

        }
        else{

        echo "Las contraseñas no coinciden";
        }
        
    }

?>
