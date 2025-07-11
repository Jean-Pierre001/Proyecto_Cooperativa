<?php
include '../includes/session.php';
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM inspectors WHERE id = :id");
        $stmt->execute([':id' => $_POST['id']]);
        header("Location: ../inspectors.php?mensaje=Inspector eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        die("Error al eliminar: " . $e->getMessage());
    }
} else {
    header("Location: ../inspectors.php");
    exit;
}
