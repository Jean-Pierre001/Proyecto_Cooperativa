<?php
require_once '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
  $stmt->execute([':id' => $_POST['id']]);
  header('Location: ../users.php');
}
