<?php
require_once '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $pdo->prepare("INSERT INTO users (email, password, type, first_name, last_name, address, contact_info, photo, created_on) VALUES (:email, :password, :type, :first_name, :last_name, :address, :contact_info, :photo, :created_on)");

  $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $photo = '';

  if (!empty($_FILES['photo']['name'])) {
    $photo = time() . '_' . $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $photo);
  }

  $stmt->execute([
    ':email' => $_POST['email'],
    ':password' => $hashed,
    ':type' => $_POST['type'],
    ':first_name' => $_POST['first_name'],
    ':last_name' => $_POST['last_name'],
    ':address' => $_POST['address'],
    ':contact_info' => $_POST['contact_info'],
    ':photo' => $photo,
    ':created_on' => $_POST['created_on']
  ]);

  header('Location: ../users.php');
}
