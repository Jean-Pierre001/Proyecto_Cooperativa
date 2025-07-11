<?php
require_once '../includes/conn.php';
session_start();

try {
    $stmt = $pdo->prepare("UPDATE members SET 
        name = :name,
        dni = :dni,
        phone = :phone,
        email = :email,
        address = :address,
        entry_date = :entry_date,
        status = :status,
        contributions = :contributions
        WHERE id = :id");

    $stmt->execute([
        ':id' => $_POST['id'],
        ':name' => $_POST['name'],
        ':dni' => $_POST['dni'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':address' => $_POST['address'],
        ':entry_date' => $_POST['entry_date'],
        ':status' => $_POST['status'],
        ':contributions' => $_POST['contributions']
    ]);

    $_SESSION['success'] = 'Member updated successfully.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error updating member: ' . $e->getMessage();
}

header('Location: ../members.php');
exit;
