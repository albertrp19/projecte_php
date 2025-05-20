<?php
    require_once('./functions.php');
    session_start();
    if(checkSession()){
        header('Location: ../html/home.php');
    }

    $user_mail_exisit = FALSE;

    if ($_SERVER["REQUEST_METHOD"] === 'POST'){
        $mail = $_POST['email'];
        $username = $_POST['username']; 
        
        //! HAY QUE HACER QUE LA CONTRASEÑA SEA FUERTE CON VALIDAICIONES
        $pass = $_POST['password']; 
        
        if($pass === $_POST['verify_password']){ //verificar ambas contraseñas son iguales

            // Validación de contraseña fuerte
            $password_errors = [];
            if (strlen($pass) < 8) {
                $password_errors[] = "La contraseña debe tener al menos 8 caracteres.";
            }
            if (!preg_match('/[A-Z]/', $pass)) {
                $password_errors[] = "La contraseña debe tener al menos una letra mayúscula.";
            }
            if (!preg_match('/[a-z]/', $pass)) {
                $password_errors[] = "La contraseña debe tener al menos una letra minúscula.";
            }
            if (!preg_match('/[0-9]/', $pass)) {
                $password_errors[] = "La contraseña debe tener al menos un número.";
            }
            if (!preg_match('/[\W_]/', $pass)) {
                $password_errors[] = "La contraseña debe tener al menos un símbolo.";
            }

            if (!empty($password_errors)) {
                foreach ($password_errors as $error) {
                    echo $error . "<br>";
                }
                exit();
            }

            $firstName = $_POST['first_name'] ?? ''; 
            $lastName = $_POST['last_name'] ?? ''; 
    
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
