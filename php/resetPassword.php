<?php
require_once('./functions.php');
session_start();

if (!isset($_GET['token']) || !isset($_GET['mail'])) {
    echo "Paràmetres no vàlids.";
    exit();
}

$token = $_GET['token'];
$mail = $_GET['mail'];

$sql = 'SELECT * FROM users WHERE mail = ? AND resetPassCode = ?';
$stmt = $db->prepare($sql);
$stmt->execute([$mail, $token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Token o usuari invàlid.";
    exit();
}

$expiry = strtotime($user['resetPassExpiry']);
if (time() > $expiry) {
    echo "Aquest enllaç ha caducat.";
    exit();
}

// Si el formulari ha estat enviat
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $pass1 = $_POST['password'];
    $pass2 = $_POST['verify_password'];

    if ($pass1 !== $pass2) {
        echo "Les contrasenyes no coincideixen.";
    } elseif (strlen($pass1) < 8) {
        echo "La contrasenya ha de tenir almenys 8 caràcters.";
    } else {
        $hash = password_hash($pass1, PASSWORD_BCRYPT);
        $update_sql = 'UPDATE users SET passHash = ?, resetPassCode = NULL, resetPassExpiry = NULL WHERE mail = ?';
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([$hash, $mail]);

        // Enviar mail de confirmació
        $htmlContent = '
        <html><body style="font-family: Arial, sans-serif;">
        <div style="text-align:center;">
            <h2>Contrasenya actualitzada</h2>
            <p>La teva contrasenya ha estat actualitzada correctament.</p>
        </div></body></html>';
        $subject = "Contrasenya canviada correctament";
        sendMailCode('', $mail, $htmlContent, $subject);

        echo "
        <script>
            alert('Contrasenya actualitzada correctament!');
            window.location.href = '../index.html';
        </script>";
        exit();
    }
}
?>

<!-- Formulari de reset -->
<form method="POST">
    <h2>Introdueix la nova contrasenya</h2>
    <input type="password" name="password" placeholder="Nova contrasenya" required><br>
    <input type="password" name="verify_password" placeholder="Repeteix contrasenya" required><br>
    <button type="submit">Actualitza contrasenya</button>
</form>
