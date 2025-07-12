<?php
session_start();
include 'includes/conn.php';

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
