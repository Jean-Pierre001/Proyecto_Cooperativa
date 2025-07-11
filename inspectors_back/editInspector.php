<?php
include '../includes/session.php';
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $sql = "UPDATE inspectors SET name = :name, level_modality = :level_modality, phone = :phone, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $_POST['name'],
            ':level_modality' => $_POST['level_modality'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':id' => $_POST['id']
        ]);
        header("Location: ../inspectors.php?mensaje=Inspector actualizado correctamente");
        exit;
    } catch (PDOException $e) {
        die("Error al actualizar: " . $e->getMessage());
    }
} else {
    header("Location: ../inspectors.php");
    exit;
}
