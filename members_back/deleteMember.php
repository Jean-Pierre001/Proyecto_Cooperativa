<?php
include '../includes/session.php';

if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit();
}

require_once '../includes/conn.php';

function sanitizeFolderName($name) {
    $name = strtolower(trim($name));
    return preg_replace('/[^a-z0-9]+/i', '_', $name);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        // Obtener datos del socio antes de eliminar
        $stmt = $pdo->prepare("SELECT name, work_site FROM members WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $member = $stmt->fetch();

        if ($member) {
            $folder_name = sanitizeFolderName($member['name']);
            $work_site = $member['work_site'];
            $folder_path = "../uploads/$work_site/$folder_name/";

            // Obtener y eliminar archivos del socio
            $stmtDocs = $pdo->prepare("SELECT file_path FROM member_documents WHERE member_id = :id");
            $stmtDocs->execute([':id' => $id]);
            $documents = $stmtDocs->fetchAll();

            foreach ($documents as $doc) {
                $file = '../uploads/' . $dc['file_path'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Eliminar registros de documentos
            $delDocs = $pdo->prepare("DELETE FROM member_documents WHERE member_id = :id");
            $delDocs->execute([':id' => $id]);

            // Eliminar carpeta si existe
            if (is_dir($folder_path)) {
                rmdir($folder_path);
            }

            // Eliminar miembro
            $delMember = $pdo->prepare("DELETE FROM members WHERE id = :id");
            $delMember->execute([':id' => $id]);

            $_SESSION['success'] = "Socio eliminado correctamente.";
        } else {
            $_SESSION['error'] = "Socio no encontrado.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al eliminar socio: " . $e->getMessage();
    }

    header('Location: ../members.php');
    exit();
}
