<?php
require_once '../includes/conn.php';
session_start();

try {
    $document = null;

    if (!empty($_FILES['document']['name'])) {
        $uploadDir = 'uploads/';
        $document = time() . '_' . basename($_FILES['document']['name']);
        move_uploaded_file($_FILES['document']['tmp_name'], $uploadDir . $document);
    }

    $stmt = $pdo->prepare("INSERT INTO members (name, dni, phone, email, address, entry_date, status, contributions, document)
                           VALUES (:name, :dni, :phone, :email, :address, :entry_date, :status, :contributions, :document)");
    $stmt->execute([
        ':name' => $_POST['name'],
        ':dni' => $_POST['dni'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':address' => $_POST['address'],
        ':entry_date' => $_POST['entry_date'],
        ':status' => $_POST['status'],
        ':contributions' => $_POST['contributions'],
        ':document' => $document
    ]);

    $_SESSION['success'] = 'Member added successfully.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error adding member: ' . $e->getMessage();
}

header('Location: ../members.php');
exit;
