<?php
require_once('../php/functions.php');
session_start();

if(!checkSession()){
    header('Location: ../index.html');
}

$posts = showPosts($db);


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
            background-position: center center;
            background-attachment: fixed;
        }
        .btn-linkup {
            display: inline-block;
            padding: 10px 26px;
            margin: 10px 10px 20px 0;
            font-size: 1em;
            font-weight: 500;
            color: #fff;
            background: #4e54c8;
            border: none;
            border-radius: 22px;
            box-shadow: 0 2px 8px rgba(78,84,200,0.10);
            text-decoration: none;
            transition: background 0.2s, box-shadow 0.2s, transform 0.1s;
            cursor: pointer;
            width: auto; /* Hace que el botón solo ocupe el ancho del texto */
        }
        .btn-linkup:hover, .btn-linkup:focus {
            background: #6c70e5;
            box-shadow: 0 4px 16px rgba(78,84,200,0.18);
            transform: translateY(-2px) scale(1.03);
            outline: none;
        }
        .logout-top-right {
            position: absolute;
            top: 20px;
            right: 30px;
            margin: 0;
            z-index: 10;
        }
        .logout-top-right input[type="submit"].btn-linkup {
            margin: 0;
            padding: 8px 20px;
            font-size: 1em;
            border-radius: 22px;
            width: auto;
        }
    </style>

<body>

<h1>Benvingut <?php echo $_SESSION['user'] ?>!!</h1>

<a href="./profile.php" class="btn-linkup">Perfil</a>
<a href="./addPost.html" class="btn-linkup">Afegir Post</a>

<form action="../php/logout.php" class="logout-form logout-top-right">
    <input type="submit" value="Tancar Sessio" class="btn-linkup">
</form>

<div class="posts-container">
<?php echo $posts;?>
</div>
    
</body>
</html>