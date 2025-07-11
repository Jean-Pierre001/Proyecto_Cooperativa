<?php
session_start();
include 'includes/conn.php';

if (isset($_POST['save'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'] ?? '';          // Opcional, chequeá si está en el formulario
    $contact_info = $_POST['contact_info'] ?? ''; // Opcional
    $curr_password = $_POST['curr_password'];
    $id = $_SESSION['user_data']['id'];

    try {
        // Buscar usuario actual
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $admin = $stmt->fetch();

        if (!$admin) {
            die('Usuario no encontrado.');
        }

        // Verificar contraseña actual
        if (!password_verify($curr_password, $admin['password'])) {
            $_SESSION['error'] = "Contraseña actual incorrecta.";
            header('Location: ' . $_GET['return']);
            exit();
        }

        // Hashear nueva contraseña solo si se ingresó una no vacía y no solo espacios
        if (!empty(trim($password))) {
            $new_password = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $new_password = $admin['password'];
        }


        // Procesar foto si hay subida
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photo_tmp = $_FILES['photo']['tmp_name'];
            $photo_name = $_FILES['photo']['name'];
            $ext = pathinfo($photo_name, PATHINFO_EXTENSION);
            $new_photo_name = uniqid() . '.' . $ext;
            $upload_path = 'assets/images/' . $new_photo_name;

            if (move_uploaded_file($photo_tmp, $upload_path)) {
                $photo_to_save = $new_photo_name;
                // Borrar foto anterior si existe
                if (!empty($admin['photo']) && file_exists('assets/images/' . $admin['photo'])) {
                    unlink('assets/images/' . $admin['photo']);
                }
            } else {
                $_SESSION['error'] = "Error al subir la foto.";
                header('Location: ' . $_GET['return']);
                exit();
            }
        } else {
            $photo_to_save = $admin['photo'];
        }

        // Actualizar datos en la BD
        $update_sql = "UPDATE users SET 
            email = :email, 
            password = :password, 
            first_name = :first_name, 
            last_name = :last_name, 
            address = :address,
            contact_info = :contact_info,
            photo = :photo 
            WHERE id = :id";

        $stmt = $pdo->prepare($update_sql);
        $success = $stmt->execute([
            'email' => $email,
            'password' => $new_password,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'address' => $address,
            'contact_info' => $contact_info,
            'photo' => $photo_to_save,
            'id' => $id
        ]);

        if ($success) {
            // Actualizar sesión
            $_SESSION['user_data']['email'] = $email;
            $_SESSION['user_data']['first_name'] = $firstname;
            $_SESSION['user_data']['last_name'] = $lastname;
            $_SESSION['user_data']['address'] = $address;
            $_SESSION['user_data']['contact_info'] = $contact_info;
            $_SESSION['user_data']['photo'] = $photo_to_save;

            $_SESSION['success'] = "Perfil actualizado correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar perfil.";
        }

        header('Location: ' . $_GET['return']);
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        header('Location: ' . $_GET['return']);
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
