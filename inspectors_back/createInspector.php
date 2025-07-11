<?php
include '../includes/session.php';
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO inspectors (name, level_modality, phone, email) VALUES (:name, :level_modality, :phone, :email)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $_POST['name'],
            ':level_modality' => $_POST['level_modality'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email']
        ]);
        header("Location: ../inspectors.php?mensaje=Inspector creado correctamente");
        exit;
    } catch (PDOException $e) {
        die("Error al insertar: " . $e->getMessage());
    }
} else {
    header("Location: ../inspectors.php");
    exit;
}
