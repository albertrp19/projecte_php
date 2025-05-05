<?php
require_once('./functions.php');
session_start();

if (checkSession()) {
    header('Location: ../html/home.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $mail = $_POST['mail']; // Camp introduït per l'usuari

    $sql = 'SELECT * FROM users WHERE mail = ?';
    $preparada = $db->prepare($sql);
    $preparada->execute([$mail]);
    $user = $preparada->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token_reset = hash('sha256', uniqid(rand(), true));
        $expiry_time = date("Y-m-d H:i:s", time() + 1800); // 30mins de validesa

        $update_sql = 'UPDATE users SET resetPassCode = ?, resetPassExpiry = ? WHERE mail = ?';
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([$token_reset, $expiry_time, $mail]);

        forgotPassword($token_reset, $mail);

        echo "
        <script>
            alert('T\'hem enviat un correu per restablir la contrasenya');
            window.location.href = '../index.html';
        </script>";
        exit();
    } else {
        echo "Aquest correu no està registrat.";
    }
}
?>
