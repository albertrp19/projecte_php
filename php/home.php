<?php
require_once('./functions.php');
session_start();

if(!checkSession()){
    header('Location: ../index.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkUp</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            background-image: url('../img/logo.png');
            background-size: cover; /* Ajusta el tamaño */
            background-position: center; /* Centra la imagen */
            background-repeat: no-repeat; /* Evita que se repita */
        }
    </style>
</head>
<body>

<h1>Benvingut <?php echo $_SESSION['user'] ?>!!</h1>



<form action="./logout.php">
    <input type="submit" value="Tancar Sessio">
</form>

    
</body>
</html>