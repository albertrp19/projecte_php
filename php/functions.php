<?php
    require_once(__DIR__ . '/connexioDB.php');
    use  PHPMailer\PHPMailer\PHPMailer;    
    require  'vendor/autoload.php';
  
    function checkSession(){
        return (isset($_SESSION['user']));
    
    }

    function existsUser($username, $postUser, $postMail){
        return (($username['username'] == $postUser || $username['mail'] == $postMail));

    }

    function checkUser($username, $postUser, ){
        return (($username['username'] == $postUser || $username['mail'] == $postUser));
    }

    function checkPasswd( $username,$postPass){
        return  password_verify($postPass, $username['passHash']);
    }

    function checkActive($username){
        return $username['active'] == 1;
    }

    function setSession($username){
        $_SESSION['user'] = $username['username'];
        $_SESSION['nom'] = $username['userFirstName'];
        $_SESSION['cognom'] = $username['userLastName'];
        setcookie("username", $username['username'], time() + (7 * 24 * 60 * 60), "/");
        ultimAcces($username['lastSignIn']);
    }

    function ultimAcces($lastSignIn){
        setcookie("ultima_data",$lastSignIn, time() + (7 * 24 * 60 * 60), "/"); 
        
    }

    function updateLastSignIn($user, $db){
        $sql = 'UPDATE users SET lastSignIn = ? WHERE username = ?';
        $preparada = $db->prepare($sql);
        $preparada->execute(array(date("Y-m-d H:i:s"),$user));
    }

    function closeSession(){
        session_unset(); // Esborra totes les variables de sessió
        session_destroy(); // Destrueix la sessió
        setcookie("ultima_data","", time() - (7 * 24 * 60 * 60), "/");
        setcookie("username", "", time() - (7 * 24 * 60 * 60), "/");
        header("Location: ../index.html"); // Redirigeix a la pàgina d'inici
    }


    function forgotPassword($token, $mailUser){
        $htmlContent = '
        <html>
        <body style="font-family: Arial, sans-serif;">
            <div style="text-align:center;">
                <img src="http://linkup.com/projecte/img/logo.png" alt="LinkUp Logo" width="150">
                <h2>Recuperació de contrasenya</h2>
                <p>Per recuperar la contrasenya, si us plau fes clic a l’enllaç següent:</p>
                <a href="http://linkup.com/projecte/php/resetPassword.php?token=' . urlencode($token) . '&mail=' . urlencode($mailUser) . '" 
                   style="display:inline-block; padding:10px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;">
                    Recupera la teva contrasenya
                </a>
            </div>
        </body>
        </html>';
        
        $subject = "Recuperació de contrasenya";

        sendMailCode($token, $mailUser, $htmlContent, $subject);
    }
    function activeUser($token, $mailUser){
        $htmlContent = '
        <html>
        <body style="font-family: Arial, sans-serif;">
            <div style="text-align:center;">
                <img src="http://linkup.com/projecte/img/logo.png" alt="LinkUp Logo" width="150">
                <h2>Benvingut a LinkUp!</h2>
                <p>Per completar el registre, si us plau activa el teu compte fent clic a l’enllaç següent:</p>
                <a href="http://linkup.com/projecte/php/mailCheckAccount.php?token=' . urlencode($token) . '&mail=' . urlencode($mailUser) . '" 
                   style="display:inline-block; padding:10px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;">
                    Active your account Now!
                </a>
            </div>
        </body>
        </html>';
        
        $subject = "Benvingut a LinkUp! Activa el teu compte";

        sendMailCode($token, $mailUser, $htmlContent, $subject);

    }
    function sendMailCode($token, $mailUser, $htmlContent, $subject){
        
        
        //$attatchment = $_FILES["file"];
        //$tmp_name = $attatchment["tmp_name"];
        require  'vendor/autoload.php';
        $mail  =  new  PHPMailer();
        $mail->IsSMTP();
        //Configuració  del  servidor  de  Correu
        //Modificar  a  0  per  eliminar  msg  error
        $mail->SMTPDebug  =  0;
        $mail->SMTPAuth  =  true;
        $mail->SMTPSecure  =  'tls';
        $mail->Host  =  'smtp.gmail.com';
        $mail->Port  =  587;
        
            //Credencials  del  compte  GMAIL
            $mail->Username  =  "abel.sierram@educem.net";
            $mail->Password  =  "ulve hurp dngx wjju";

        //Dades del correu electrònic
            $mail->SetFrom("abel.sierram@educem.net",'LinkUp');
            $mail->Subject= $subject;
            $mail->MsgHTML($htmlContent);

            if (!empty($tmp_name)) {
            $file_path = $att_dir . basename($filenames);
            if (move_uploaded_file($tmp_name, $file_path)){
                $mail->addAttachment($file_path);
            }
            }
            //Destinatari
            $address= $mailUser;
            $mail->AddAddress($address,"Benvingut");
            //Enviament
            
            $result=$mail->Send();
            if(!$result){
            echo'Error:'.$mail->ErrorInfo;
            }else{
            echo "Correu enviat";
            
            }
    }   //$filenames = $attatchment["name"];
        $att_dir = "archivos/";
        
    function getProfileInfo($user, $db){
        
        $sql = 'SELECT * FROM users WHERE username = ?';
        $select = $db->prepare($sql);
        $select->execute(array($user));
        return $select->fetch(PDO::FETCH_ASSOC);
    }

    function buildUserInfoHtml($infoUser) {
        $html = '';
    
        $fields = [
            'profileImage'  => $infoUser['profile_image_path'],
            'Username'      => $infoUser['username'],
            'Email'         => $infoUser['mail'],
            'First Name'    => $infoUser['first_name'] ?? null,
            'Last Name'     => $infoUser['last_name'] ?? null,
            'Phone Number'  => $infoUser['phone'] ?? null,
            'Location'      => $infoUser['location'] ?? null,
            'Birthdate'     => $infoUser['birthdate'] ?? null,
            'Description'   => $infoUser['description'] ?? null,
        ];
    
        foreach ($fields as $label => $value) {
            if (!empty($value)) {
                if ($label === 'profileImage') {
                    $safePath = htmlspecialchars($value);
                    $html .= "<img src=\"..{$safePath}\" alt=\"Profile image\" width=\"200px\" height=\"200px\">\n";
                } else {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<p>{$label}: {$safeValue}</p>\n";
                }
            }
        }
    
        return $html;
    }
    function generateForm(array $userData = [])
    {
        $fields = [
            'first_name'   => 'First Name',
            'last_name'    => 'Last Name',
            'phone_number' => 'Phone Number',
            'location'     => 'Location',
            'birthdate'    => 'Birthdate',
            'description'  => 'Description',
            'profile_image'=> 'Profile Image'
        ];
    
        echo '<form action="../html/updateProfile.php" method="POST" enctype="multipart/form-data">';
    
        foreach ($fields as $field => $label) {
            echo "<label for=\"$field\">$label</label><br>";
    
            $value = isset($userData[$field]) ? htmlspecialchars($userData[$field], ENT_QUOTES, 'UTF-8') : '';
    
            if ($field === 'description') {
                echo "<textarea id=\"$field\" name=\"$field\" rows=\"4\" cols=\"50\" value=\"$value\"></textarea><br><br>";
    
            } elseif ($field === 'profile_image') {
                if (!empty($value)) {
                    echo "<div style=\"margin-bottom:8px;\">
                            <strong>Imagen actual:</strong><br>
                            <img src=\"$value\" alt=\"Profile Image\" style=\"max-width:150px;\">
                          </div>";
                }
                echo "<input type=\"file\" id=\"$field\" name=\"$field\" accept=\"image/*\"><br><br>";
    
            } elseif ($field === 'birthdate') {
                echo "<input type=\"date\" id=\"$field\" name=\"$field\" value=\"$value\"><br><br>";
    
            } else {
                echo "<input type=\"text\" id=\"$field\" name=\"$field\" value=\"$value\"><br><br>";
            }
        }
    
        echo '<button type="submit">Guardar cambios</button>';
        echo '</form>';
    }
    
?>