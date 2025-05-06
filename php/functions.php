<?php
    require_once(__DIR__ . '/connexioDB.php');
    use  PHPMailer\PHPMailer\PHPMailer;    
    require  'vendor/autoload.php';
    # Funció per comprovar si la sessió està activa
    function checkSession(){
        return (isset($_SESSION['user']));
    
    }
    # Funció per comprovar si l'usuari existeix a la base de dades
    function existsUser($username, $postUser, $postMail){
        return (($username['username'] == $postUser || $username['mail'] == $postMail));

    }
    # Funció per comprovar si l'usuari/correu son correctes (login)
    function checkUser($username, $postUser){
        return (($username['username'] == $postUser || $username['mail'] == $postUser));
    }
    # Funció per comprovar si la contrasenya és correcta
    function checkPasswd( $username,$postPass){
        return  password_verify($postPass, $username['passHash']);
    }
    # Funció per comprovar si l'usuari està actiu
    function checkActive($username){
        return $username['active'] == 1;
    }
    # Funcio per iniciar sessio i guardar les dades de l'usuari a la sessió
    function setSession($username){
        $_SESSION['user'] = $username['username'];
        $_SESSION['nom'] = $username['userFirstName'];
        $_SESSION['cognom'] = $username['userLastName'];
        setcookie("username", $username['username'], time() + (7 * 24 * 60 * 60), "/");
        ultimAcces($username['lastSignIn']);
    }
    # Funció per guardar la data de l'últim accés a la cookie
    function ultimAcces($lastSignIn){
        setcookie("ultima_data",$lastSignIn, time() + (7 * 24 * 60 * 60), "/"); 
    }
    # Funció per actualitzar la data de l'últim accés a la base de dades
    function updateLastSignIn($user, $db){
        $sql = 'UPDATE users SET lastSignIn = ? WHERE username = ?';
        $preparada = $db->prepare($sql);
        $preparada->execute(array(date("Y-m-d H:i:s"),$user));
    }

    # Tanca la sessio i esborra les cookies
    function closeSession(){
        session_unset(); // Esborra totes les variables de sessió
        session_destroy(); // Destrueix la sessió
        setcookie("ultima_data","", time() - (7 * 24 * 60 * 60), "/");
        setcookie("username", "", time() - (7 * 24 * 60 * 60), "/");
        header("Location: ../index.html"); // Redirigeix a la pàgina d'inici
    }

    # Funció per enviar un correu electrònic per recuperar la contrasenya
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
    # Funció per enviar un correu electrònic per activar el compte d'usuari
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
    # Funció per enviar un correu electrònic amb el contingut HTML
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
    }    
    # Funcio per agafar tota la info de l'usuari
    function getProfileInfo($user, $db){
        
        $sql = 'SELECT * FROM users WHERE username = ?';
        $select = $db->prepare($sql);
        $select->execute(array($user));
        return $select->fetch(PDO::FETCH_ASSOC);
    }
    function updateProfile($postInfo, $files, $db) {
        $username = $_SESSION['user'];
        $fields = [];
        $params = [];
    
        // Mapea los nombres del formulario a los de la base de datos
        $fieldMap = [
            'userFirstName',
            'userLastName',
            'phone',
            'location',
            'birthdate',
            'description'
        ];
    
        foreach ($fieldMap as $postField) {
            if (isset($postInfo[$postField]) && $postInfo[$postField] !== '') {
                $fields[] = "$postField = ?";
                $params[] = htmlspecialchars($postInfo[$postField], ENT_QUOTES, 'UTF-8');
            }
        }
    
        // Imagen
        if (!empty($files['profile_image']['tmp_name'])) {
            $uploadDir = '/img/uploads/profile/';
            $fileName = basename($files['profile_image']['name']);
            $uploadPath = $uploadDir . uniqid() . '_' . $fileName;
            move_uploaded_file($files['profile_image']['tmp_name'], __DIR__ . '/..' . $uploadPath);
    
            $fields[] = "profile_image_path = ?";
            $params[] = $uploadPath;
        }
    
        if (empty($fields)) return;
    
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE username = ?";
        $params[] = $username;
    
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }
    
    function addPost($content, $user, $file, $db) {

        if (!empty($file['tmp_name'])) {
            $uploadDir = '/img/uploads/posts/';
            $fileName = basename($file['name']);
            $uploadPath = $uploadDir . uniqid() . '_' . $fileName;
            move_uploaded_file($file['tmp_name'], __DIR__ . '/..' . $uploadPath);
    
            
        }
        else{

            $uploadPath = NULL;

        }
        $sql = 'INSERT INTO posts (content, user_id, image_path) VALUES (?, ?, ?)';
    
        $stmt = $db->prepare($sql);
        $stmt->execute([$content, $user, $uploadPath]);
        
    }

    function showPosts($db) {
        $sql = 'SELECT * FROM posts ORDER BY creationDate DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $html = '';

        foreach ($posts as $post) {
            $postId = htmlspecialchars($post['id_post'], ENT_QUOTES, 'UTF-8');
            $content = nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'));
            $imagePath = htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8');
            $createdAt = htmlspecialchars($post['creationDate'], ENT_QUOTES, 'UTF-8');
    
            $html .= "<div class=\"post\" id=\"post-$postId\">";
            $html .= "<p>$content</p>";
            if (!empty($imagePath)) {
                $html .= "<img src=\"..$imagePath\" alt=\"Post image\" style=\"max-width: 100%; height: auto;\">";
            }
            $html .= "<p>Publicat el: $createdAt</p>";
            $html .= "</div>";
        }
        return $html;
    }
    # Funcio per agafar tota la info de l'usuari per mostrar-la al perfil
    function buildUserInfoHtml($infoUser) {
        $html = '';
    
        $fields = [
            'profileImage'  => $infoUser['profile_image_path'],
            'Username'      => $infoUser['username'],
            'Email'         => $infoUser['mail'],
            'Nom'           => $infoUser['userFirstName'] ?? null,
            'Cognom'        => $infoUser['userLastName'] ?? null,
            'Telefon'       => $infoUser['phone'] ?? null,
            'Ubicacio'      => $infoUser['location'] ?? null,
            'Edat'          => $infoUser['birthdate'] ?? null,
            'Descripcio'   => $infoUser['description'] ?? null,
        ];
    
        foreach ($fields as $label => $value) {
            if (!empty($value)) {
                if ($label === 'profileImage') {
                    $safePath = htmlspecialchars($value);
                    $html .= "<img src=\"..{$safePath}\" alt=\"Profile image\" width=\"200px\" height=\"200px\">\n";
                } elseif ($label === 'Edat') {
                    $age = calculateAge($value);
                    $html .= "<p>{$label}: {$age} anys</p>\n";
                } elseif ($label === 'Descripcio') {
                    $safeValue = nl2br(htmlspecialchars($value));
                    $html .= "<p>{$label}: {$safeValue}</p>\n";
                }
                elseif ($label === 'Ubicacio') {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<p>{$label}: <a href=\"https://www.google.com/maps/search/?api=1&query={$safeValue}\" target=\"_blank\">{$safeValue}</a></p>\n";
                    $html .= generateGoogleMapEmbed($safeValue);
                }
                else {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<p>{$label}: {$safeValue}</p>\n";
                }
            }
        }
    
        return $html;
    }
    # Funcio per calcular l'edat a partir de la data de naixement
    function calculateAge($birthdate) {
        $birthDate = new DateTime($birthdate);
        $currentDate = new DateTime();
        $age = $currentDate->diff($birthDate)->y;
        return $age;
    }
    # Funcio per mostrar el formulari d'actualitzacio del perfil
    function generateForm($infoUser) {
        $fields = [
            'userFirstName'    => 'First Name',
            'userLastName'     => 'Last Name',
            'phone'         => 'Phone Number',
            'location'      => 'Location',
            'birthdate'     => 'Birthdate',
            'description'   => 'Description',
            'profile_image_path' => 'Profile Image'
        ];
    
        echo '<form action="../html/updateProfile.php" method="POST" enctype="multipart/form-data">';
    
        foreach ($fields as $field => $label) {
            $value = isset($infoUser[$field]) ? htmlspecialchars($infoUser[$field], ENT_QUOTES, 'UTF-8') : '';
    
            echo "<label for=\"$field\">$label</label><br>";
    
            if ($field === 'description') {
                echo "<textarea id=\"$field\" name=\"$field\" rows=\"4\" cols=\"50\">$value</textarea><br><br>";
    
            } elseif ($field === 'profile_image_path') {
                if (!empty($value)) {
                    echo "<div style=\"margin-bottom:8px;\">
                            <strong>Imagen actual:</strong><br>
                            <img src=\"..$value\" alt=\"Profile Image\" style=\"max-width:150px;\">
                          </div>";
                }
                echo "<input type=\"file\" id=\"$field\" name=\"profile_image\" accept=\"image/*\"><br><br>";
    
            } elseif ($field === 'birthdate') {
                echo "<input type=\"date\" id=\"$field\" name=\"$field\" value=\"$value\"><br><br>";
    
            } else {
                echo "<input type=\"text\" id=\"$field\" name=\"$field\" value=\"$value\"><br><br>";
            }
        }
    
        echo '<button type="submit">Guardar cambios</button>';
        echo '</form>';
    }
    
    function generateGoogleMapEmbed($address) {
        $encodedAddress = urlencode($address);
        $iframe = "<iframe 
            width=\"400\" 
            height=\"300\" 
            style=\"border:0;\" 
            loading=\"lazy\" 
            allowfullscreen 
            referrerpolicy=\"no-referrer-when-downgrade\" 
            src=\"https://www.google.com/maps/embed/v1/place?key=AIzaSyB2NIWI3Tv9iDPrlnowr_0ZqZWoAQydKJU&q={$encodedAddress}\">
        </iframe>";
    
    return $iframe;
    }
    
?>