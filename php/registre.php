<?php
    require_once('./functions.php');
    session_start();
    if(checkSession()){
        header('Location: ./php/home.php');
    }

    $user_mail_exisit = FALSE;

    if ($_SERVER["REQUEST_METHOD"] === 'POST'){
        $mail = $_POST['email']; // Varchar(40) (Unique)
        $username = $_POST['username']; // Varchar(16) (Unique)
        
        //! HAY QUE HACER QUE LA CONTRASEÑA SEA FUERTE CON VALIDAICIONES
        $pass = $_POST['password']; // Varchar(60)
        
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
            $sql_insert = 'INSERT INTO users(mail,username,passHash,userFirstName,userLastName,creationDate,removeDate,lastSignIn) VALUES(?,?,?,?,?,?,?,?)';
            $preparada = $db->prepare($sql_insert);
            $preparada->execute(array($mail,$username,password_hash($pass, PASSWORD_BCRYPT),$firstName,$lastName,date("Y-m-d H:i:s"), NULL,date("Y-m-d H:i:s")));
            echo "
            <script>
                alert('Registre realitzat correctament! Accepta per continuar');
                window.location.href = '../index.php'; // Redirigir después del mensaje
            </script>";
            exit();
           
        }else {
            echo "L'usuari/mail ja estan registrat";
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrat</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<form action="./registre.php" method="POST">
        <label for="username">Username (Obligatorio):</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email (Obligatorio):</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="first_name">First Name (Opcional):</label><br>
        <input type="text" id="first_name" name="first_name"><br><br>

        <label for="last_name">Last Name (Opcional):</label><br>
        <input type="text" id="last_name" name="last_name"><br><br>

        <label for="password">Password (Obligatorio):</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="verify_password">Verify Password (Obligatorio):</label><br>
        <input type="password" id="verify_password" name="verify_password" required><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>