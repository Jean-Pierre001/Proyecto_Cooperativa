<?php
require_once '../includes/conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM members WHERE id = :id");
        $stmt->execute([':id' => $_POST['id']]);
        $_SESSION['success'] = 'Member deleted successfully.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error deleting member: ' . $e->getMessage();
    }
}

header('Location: ../members.php');
exit;
