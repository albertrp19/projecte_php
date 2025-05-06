<?php
require_once('../php/functions.php');
session_start();

if(!checkSession()){
    header('Location: ../index.html');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkUp</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background-image: url('../img/logo.png');
            background-repeat: no-repeat;
            height: 100vh;
            background-position: center center;
        }
    </style>
</head>
<body>

<h1>Benvingut <?php echo $_SESSION['user'] ?>!!</h1>



<a href="./profile.php">Perfil</a>

a

<form action="./logout.php">
    <input type="submit" value="Tancar Sessio">
</form>

    
</body>
</html>