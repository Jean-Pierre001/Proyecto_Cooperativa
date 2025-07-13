<?php
session_start();
include 'includes/conn.php';

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
        $conn = $pdo->open();
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $_SESSION['user_data'] = $user;
        }
        $pdo->close();
    } catch (PDOException $e) {
        echo "Connection error: " . $e->getMessage();
    }
}
?>
