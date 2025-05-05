<?php
session_start();
require_once('functions.php');
require_once('connexioDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mail'], $_GET['token'])) {

    $sql = "SELECT * FROM users WHERE mail = ?";
    $select = $db->prepare($sql);
    $select->execute([$_GET['mail']]);
    $result = $select->fetch(PDO::FETCH_ASSOC);
    if ($result && $result['activationCode'] === $_GET['token']) {
        $sql = "UPDATE users SET active = '1', activationDate = ?, activationCode = NULL WHERE mail = ?";
        $update = $db->prepare($sql);
        $update->execute([date("Y-m-d H:i:s"), $_GET['mail']]);

        // Pasar $result (array asociativo) a setSession
        setSession($result);

        // Redirigir a home.php
        header('Location: ../html/home.php');
        exit();
    } else {
        header('Location: ../index.html?verificat=error');
        exit();
    }
} else {
    header('Location: ../index.html?verificat=error');
    exit();
}
?>