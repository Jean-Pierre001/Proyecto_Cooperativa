<?php
include 'includes/session.php'; // Asegura que session_start() ya se llamó
include_once 'includes/conn.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Usamos $pdo directamente para preparar y ejecutar la consulta
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Verificar contraseña
            if (password_verify($password, $user['password'])) {
                // Guardar datos del usuario en sesión
                $_SESSION['user'] = $user;        // Datos generales para lógica
                $_SESSION['user_data'] = $user;   // Datos para mostrar en navbar

                // Redirigir según tipo de usuario (ajustá según tus tipos)
                switch ($user['type']) {
                    case 1:
                        header('Location: index.php');
                        break;
                    default:
                        header('Location: index.php');
                        break;
                }
                exit();
            } else {
                $_SESSION['error'] = 'Contraseña incorrecta.';
                header('Location: login.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        header('Location: login.php');
        exit();
    }

} else {
    // Acceso directo sin pasar por formulario login
    header('Location: login.php');
    exit();
}
?>
