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
        $_SESSION['user_id'] = $username['iduser'];
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
        ybo yyle="fonfontmfimily: Aryal, saas-s, ifsans-serif;">
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
    // Comprobar si se ha subido un archivo
    if (!empty($file['file']['tmp_name'])) {
        // Directorio de destino
        $uploadDir = '/img/uploads/posts/';
        $fileName = basename($file['name']);
        // Generamos un nombre único para el archivo
        $uploadPath = $uploadDir . uniqid() . '_' . $fileName;
        // Mover el archivo subido al directorio de destino
        move_uploaded_file($file['file']['tmp_name'], __DIR__ . '/..' . $uploadPath);
    } else {
        // Si no se subió ningún archivo, asignar NULL
        $uploadPath = NULL;
    }

    // Inserción en la base de datos
    $sql = 'INSERT INTO posts (content, user_id, image_path) VALUES (?, ?, ?)';
    // Preparar y ejecutar la consulta
    $stmt = $db->prepare($sql);
    $stmt->execute([$content, $user, $uploadPath]);
}


    function showPosts($db) {
     // 1) Consulta única con JOIN para traer los posts y su autor
$sql = '
  SELECT
    p.id_post,
    p.user_id,
    p.content,
    p.image_path,
    p.creationDate,
    p.likes_count,
    u.username AS autor
  FROM posts AS p
  JOIN users AS u
    ON p.user_id = u.iduser
  ORDER BY p.creationDate DESC
';
$stmt = $db->prepare($sql);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '';

// 2) Recorremos ya con el campo 'autor' disponible
foreach ($posts as $post) {
    $user_id  = htmlspecialchars($post['user_id'],     ENT_QUOTES, 'UTF-8');
    $postId    = htmlspecialchars($post['id_post'],    ENT_QUOTES, 'UTF-8');
    $autor     = htmlspecialchars($post['autor'],      ENT_QUOTES, 'UTF-8');
    $content   = nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8'));
    $imagePath = htmlspecialchars($post['image_path'], ENT_QUOTES, 'UTF-8');
    $createdAt = htmlspecialchars($post['creationDate'],ENT_QUOTES, 'UTF-8');
    $likesCount = htmlspecialchars($post['likes_count'], ENT_QUOTES, 'UTF-8');

    $sqlUser = 'SELECT * FROM users WHERE iduser = ?';
    $stmtUser = $db->prepare($sqlUser);
    $stmtUser->execute([$user_id]);
    $autor = $stmtUser->fetch(PDO::FETCH_ASSOC);
    $autorName = htmlspecialchars($autor['username'], ENT_QUOTES, 'UTF-8');

     
            $html .= "<div class=\"post\" id=\"post-$postId\">";
            $html .= "<div class=\"post-author\">$autorName</div>";
            $html .= "<div class=\"post-date\">Publicat el: $createdAt</div>";
            $html .= "<div class=\"post-content\">$content</div>";
            if (!empty($imagePath)) {
                $html .= "<img src=\"..$imagePath\" alt=\"Post image\">";
            }
            $html .= "<div class=\"likes\">Likes: $likesCount</div>";
            // Botón de "like"
            $html .= "<form method=\"POST\" action=\"../php/likePost.php\">";
            $html .= "<input type=\"hidden\" name=\"post_id\" value=\"$postId\">";
            $html .= "<input type=\"submit\" value=\"Like\" class=\"btn-linkup\">";
            $html .= "</form>";
            // Formulario para agregar comentarios
            $html .= "<form method=\"POST\" action=\"../php/addComment.php\">";
            $html .= "<input type=\"hidden\" name=\"post_id\" value=\"$postId\">";
            $html .= "<textarea name=\"content\" placeholder=\"Escribe un comentario...\" required></textarea>";
            $html .= "<input type=\"submit\" value=\"Comentar\" class=\"btn-linkup\">";
            $html .= "</form>";
            // Mostrar comentarios
            $sqlComments = 'SELECT * FROM comments WHERE post_id = ? ORDER BY creationDate ASC';
            $stmtComments = $db->prepare($sqlComments);
            $stmtComments->execute([$postId]);
            $comments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
            $html .= "<div class=\"comments\">";
            foreach ($comments as $comment) {
                $commentContent = nl2br(htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'));
                $commentDate = htmlspecialchars($comment['creationDate'], ENT_QUOTES, 'UTF-8');
                $commentUserId = htmlspecialchars($comment['user_id'], ENT_QUOTES, 'UTF-8');
                $sqlUser = 'SELECT * FROM users WHERE iduser = ?';
                $stmtUser = $db->prepare($sqlUser);
                $stmtUser->execute([$commentUserId]);
                $commentUser = $stmtUser->fetch(PDO::FETCH_ASSOC);
                $commentUserName = htmlspecialchars($commentUser['username'], ENT_QUOTES, 'UTF-8');
                $html .= "<p><strong>$commentUserName:</strong> $commentContent</p>";
                $html .= "<p><em>Fecha: $commentDate</em></p>";
            }
            $html .= "</div>"; // Cerrar div de comentarios
            $html .= "</div>";
        }
        return $html;
    }
    # Funcio per agafar tota la info de l'usuari per mostrar-la al perfil
    function buildUserInfoHtml($infoUser) {
        $html = '<div class="profile-card">';
    
        $fields = [
            'profileImage'  => $infoUser['profile_image_path'],
            'Username'      => $infoUser['username'],
            'Email'         => $infoUser['mail'],
            'Nom'           => $infoUser['userFirstName'] ?? null,
            'Cognom'        => $infoUser['userLastName'] ?? null,
            'Telefon'       => $infoUser['phone'] ?? null,
            'Ubicacio'      => $infoUser['location'] ?? null,
            'Edat'          => $infoUser['birthdate'] ?? null,
            'Descripcio'    => $infoUser['description'] ?? null,
        ];
    
        $html = '';
        $html .= '<div class="profile-info-list">';
        $avatarPrinted = false;
        foreach ($fields as $label => $value) {
            if (!empty($value)) {
                if ($label === 'profileImage') {
                    $safePath = htmlspecialchars($value);
                    $html .= "<div class='profile-avatar'><img src='..{$safePath}' alt='Imatge de perfil'></div>";
                    $avatarPrinted = true;
                } elseif ($label === 'Username') {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<h3 class='profile-username'>@$safeValue</h3>";
                } elseif ($label === 'Edat') {
                    $age = calculateAge($value);
                    $html .= "<div class='profile-row'><span class='profile-label'>{$label}:</span> <span class='profile-value'>{$age} anys</span></div>";
                } elseif ($label === 'Descripcio') {
                    $safeValue = nl2br(htmlspecialchars($value));
                    $html .= "<div class='profile-row'><span class='profile-label'>{$label}:</span> <span class='profile-value'>$safeValue</span></div>";
                } elseif ($label === 'Ubicacio') {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<div class='profile-row'><span class='profile-label'>{$label}:</span> <span class='profile-value'><a href='https://www.google.com/maps/search/?api=1&query={$safeValue}' target='_blank'>{$safeValue}</a></span></div>";
                    $html .= generateGoogleMapEmbed($safeValue);
                } else {
                    $safeValue = htmlspecialchars($value);
                    $html .= "<div class='profile-row'><span class='profile-label'>{$label}:</span> <span class='profile-value'>{$safeValue}</span></div>";
                }
            }
        }
        $html .= '</div>';
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
            'phone'            => 'Phone Number',
            'location'         => 'Location',
            'birthdate'        => 'Birthdate',
            'description'      => 'Description',
            'profile_image_path' => 'Profile Image',
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
    // Función para dar "like" a un post, asegurando que un usuario solo pueda dar "like" una vez
    function likePost($postId, $db) {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('El ID del usuario no está definido en la sesión.');
        }

        $userId = $_SESSION['user_id'];

        // Verificar si el usuario ya ha dado "like" al post
        $sqlCheck = 'SELECT COUNT(*) FROM likes WHERE post_id = ? AND user_id = ?';
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([$postId, $userId]);
        $alreadyLiked = $stmtCheck->fetchColumn();

        if ($alreadyLiked > 0) {
            return; // El usuario ya ha dado "like", no hacer nada
        }

        // Registrar el "like" en la tabla "likes"
        $sqlInsert = 'INSERT INTO likes (post_id, user_id) VALUES (?, ?)';
        $stmtInsert = $db->prepare($sqlInsert);
        $stmtInsert->execute([$postId, $userId]);

        // Incrementar el contador de "likes" en la tabla "posts"
        $sqlUpdate = 'UPDATE posts SET likes_count = likes_count + 1 WHERE id_post = ?';
        $stmtUpdate = $db->prepare($sqlUpdate);
        $stmtUpdate->execute([$postId]);
    }
    // Función para agregar un comentario a un post
    function addComment($postId, $userId, $content, $db) {
        $sql = 'INSERT INTO comments (post_id, user_id, content, creationDate) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute([$postId, $userId, $content, date('Y-m-d H:i:s')]);
    }
    
?>