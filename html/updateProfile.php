<?php
require_once('../php/functions.php');
    
session_start();
if(!checkSession()){
    header('Location: ../index.html');
}
$infoUser = getProfileInfo($_SESSION['user'], $db);

generateForm($infoUser);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualitza el teu perfil</title>
</head>
<body>


</body>
</html>