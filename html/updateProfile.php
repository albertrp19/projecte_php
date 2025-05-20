<?php
require_once('../php/functions.php');
    
session_start();
if(!checkSession()){
    header('Location: ../index.html');
}
$htmlForm = generateForm(getProfileInfo($_SESSION['user'], $db));

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    updateProfile($_POST, $_FILES, $db);

    header('Location: ../html/profile.php');
    
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualitza el teu perfil</title>
</head>
<body>
    <?php echo $htmlForm; ?>

</body>
</html>