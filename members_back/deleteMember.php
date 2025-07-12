<?php
require_once '../includes/conn.php';
session_start();

try {
    if (!isset($_POST['id'])) {
        $_SESSION['error'] = 'ID no especificado.';
        header('Location: ../members.php');
        exit();
    }

    $memberId = $_POST['id'];

    // Obtener archivos asociados al socio
    $stmt = $pdo->prepare("SELECT file_path FROM member_documents WHERE member_id = :id");
    $stmt->execute([':id' => $memberId]);
    $documents = $stmt->fetchAll();

    // Ruta absoluta base uploads
    $uploadDir = __DIR__ . '/../uploads/';

    // Eliminar archivos del servidor
    foreach ($documents as $doc) {
        $file = $uploadDir . $doc['file_path'];
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Eliminar registros de documentos
    $stmt = $pdo->prepare("DELETE FROM member_documents WHERE member_id = :id");
    $stmt->execute([':id' => $memberId]);

    // Eliminar al miembro
    $stmt = $pdo->prepare("DELETE FROM members WHERE id = :id");
    $stmt->execute([':id' => $memberId]);

    // Intentar eliminar la carpeta del socio si está vacía
    $memberFolder = $uploadDir . $memberId;
    if (is_dir($memberFolder)) {
        $files = scandir($memberFolder);
        if (count($files) <= 2) { // solo '.' y '..'
            rmdir($memberFolder);
        }
    }

    $_SESSION['success'] = 'Socio y documentos eliminados correctamente.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al eliminar socio: ' . $e->getMessage();
}

header('Location: ../members.php');
exit();
