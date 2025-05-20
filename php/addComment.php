<?php
require_once('functions.php');
session_start();

if (!checkSession()) {
    header('Location: ../index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $content = $_POST['content'];
    $infoUser = getProfileInfo($_SESSION['user'], $db);
    $userId = $infoUser['iduser'];

    addComment($postId, $userId, $content, $db);
    header('Location: ../html/home.php');
    exit;
}

header('Location: ../html/home.php');
exit;
