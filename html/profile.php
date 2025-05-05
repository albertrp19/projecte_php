<?php
    require_once('../php/functions.php');
    
    session_start();
    if(!checkSession()){
        header('Location: ../index.html');
    }
    $infoUser = getProfileInfo($_SESSION['user'], $db);
   

    $htmlInfo = buildUserInfoHtml($infoUser);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <?php echo $htmlInfo; ?>

    <a href="./updateProfile.php">Edita el teu perfil</a>
    
</body>
</html>