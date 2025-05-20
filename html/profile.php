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
    <div style="min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; background: none;">
        <a href="./home.php" class="btn-linkup" style="align-self: flex-start; margin: 32px 0 0 32px;">⟵ Tornar</a>
        <div class="profile-card" style="margin-top: 48px;">
            <h2 style="color: #4e54c8; margin-bottom: 18px; font-size: 2em;">El meu perfil</h2>
            <?php echo $htmlInfo; ?>
            <a href="./updateProfile.php" class="btn-linkup" style="margin-top: 18px;">Edita el teu perfil</a>
        </div>
    </div>
</body>
</html>