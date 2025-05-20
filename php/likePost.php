<?php
require_once('functions.php');
session_start();

if (!checkSession()) {
    header('Location: ../index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    likePost($postId, $db);
    header('Location: ../html/home.php');
    exit;
}

header('Location: ../html/home.php');
exit;
