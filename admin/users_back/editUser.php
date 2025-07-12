<?php
require_once '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $sql = "UPDATE users SET email = :email, type = :type, first_name = :first_name, last_name = :last_name, address = :address, contact_info = :contact_info, created_on = :created_on";

  if (!empty($_POST['password'])) {
    $sql .= ", password = :password";
  }

  if (!empty($_FILES['photo']['name'])) {
    $photo = time() . '_' . $_FILES['photo']['name'];
    move_uploaded_file($_FILES['photo']['tmp_name'], '../uploads/' . $photo);
    $sql .= ", photo = :photo";
  }

  $sql .= " WHERE id = :id";
  $stmt = $pdo->prepare($sql);

  $params = [
    ':email' => $_POST['email'],
    ':type' => $_POST['type'],
    ':first_name' => $_POST['first_name'],
    ':last_name' => $_POST['last_name'],
    ':address' => $_POST['address'],
    ':contact_info' => $_POST['contact_info'],
    ':created_on' => $_POST['created_on'],
    ':id' => $_POST['id']
  ];

  if (!empty($_POST['password'])) {
    $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
  }

  if (!empty($_FILES['photo']['name'])) {
    $params[':photo'] = $photo;
  }

  $stmt->execute($params);

  header('Location: ../users.php');
}
