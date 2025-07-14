<?php
// Configurar cookie de sesión correctamente para InfinityFree (sin HTTPS real)
$is_https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

// Establecer parámetros seguros de la cookie
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false, // ⚠️ debe ser false en InfinityFree
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'conn.php';

// Protección contra secuestro de sesión (IP + navegador)
if (isset($_SESSION['user'])) {
    if (
        !isset($_SESSION['ip'], $_SESSION['user_agent']) ||
        $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR'] ||
        $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']
    ) {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }
}

// Cargar datos del usuario si falta
if (!isset($_SESSION['user_data']) && isset($_SESSION['user']) && is_numeric($_SESSION['user'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_data'] = $user;
        }
    } catch (PDOException $e) {
        echo "Connection error: " . $e->getMessage();
    }
}
?>
