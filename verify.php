<?php
// Solo forzar HTTPS si no estás en localhost
if ($_SERVER['HTTP_HOST'] !== 'localhost' && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit;
}

// Configuración segura de cookies
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => ($_SERVER['HTTP_HOST'] !== 'localhost'),
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

include 'includes/session.php';
include_once 'includes/conn.php';

// Control de intentos fallidos
function registrar_intento_fallido($ip, $pdo) {
    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip, timestamp) VALUES (:ip, NOW())");
    $stmt->execute(['ip' => $ip]);
}

function contar_intentos_fallidos($ip, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = :ip AND timestamp > (NOW() - INTERVAL 15 MINUTE)");
    $stmt->execute(['ip' => $ip]);
    return $stmt->fetchColumn();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $ip = $_SERVER['REMOTE_ADDR'];

    // Limitar intentos
    if (contar_intentos_fallidos($ip, $pdo) >= 5) {
        $_SESSION['error'] = 'Demasiados intentos fallidos. Intenta más tarde.';
        header('Location: login.php');
        exit();
    }

    // Verificar reCAPTCHA
    if (!isset($_POST['g-recaptcha-response'])) {
        $_SESSION['error'] = 'Por favor completa el reCAPTCHA.';
        header('Location: login.php');
        exit();
    }

    $recaptcha = $_POST['g-recaptcha-response'];
    $secretKey = '6Lcw6oErAAAAALjcy46f90tadvmIRm3W9SlKENIh'; // 🔒 Reemplaza esto por tu clave secreta real de Google reCAPTCHA

    // Usar file_get_contents() para verificar el captcha
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptcha}&remoteip={$ip}");
    $response = json_decode($verify);

    if (!$response->success) {
        $_SESSION['error'] = 'Error de verificación reCAPTCHA. Intenta otra vez.';
        header('Location: login.php');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user'] = $user;
            $_SESSION['user_data'] = $user;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

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
            registrar_intento_fallido($ip, $pdo);
            usleep(500000); // pequeño delay
            $_SESSION['error'] = 'Credenciales inválidas.';
            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error de base de datos.';
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
