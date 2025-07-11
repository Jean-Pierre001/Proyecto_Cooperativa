<?php
include '../includes/session.php';
include '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? 0;

    if (!$id) {
        $_SESSION['error'] = "ID de escuela inválido.";
        header("Location: ../schools.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM schools WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['success'] = "Escuela eliminada correctamente.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar la escuela: " . $e->getMessage();
    }

    header("Location: ../schools.php");
    exit;
} else {
    header("Location: ../schools.php");
    exit;
}
?>
