<?php
include '../includes/session.php';
if (!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit();
}
require_once '../includes/conn.php';

function sanitizeFolderName($name) {
    $name = strtolower(trim($name));
    $name = preg_replace('/[^a-z0-9]+/i', '_', $name);
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    $member_number = $_POST['member_number'] ?? '';
    $name = $_POST['name'] ?? '';
    $cuil = $_POST['cuil'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';
    $entry_date = $_POST['entry_date'] ?? null;
    $exit_date = $_POST['exit_date'] ?? null;
    $status = $_POST['status'] ?? 'activo';
    $work_site = $_POST['work_site'] ?? '';

    try {
        // Actualizar socio
        $stmt = $pdo->prepare("UPDATE members SET member_number = :member_number, name = :name, cuil = :cuil, phone = :phone, email = :email, address = :address, entry_date = :entry_date, exit_date = :exit_date, status = :status, work_site = :work_site WHERE id = :id");
        $stmt->execute([
            ':member_number' => $member_number,
            ':name' => $name,
            ':cuil' => $cuil,
            ':phone' => $phone,
            ':email' => $email,
            ':address' => $address,
            ':entry_date' => $entry_date ?: null,
            ':exit_date' => $exit_date ?: null,
            ':status' => $status,
            ':work_site' => $work_site,
            ':id' => $id
        ]);

        // Crear carpeta para archivos nuevos (no mueve archivos existentes)
        $folder_name = sanitizeFolderName($name);
        $target_dir = "../uploads/$work_site/$folder_name/";

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Subir nuevos archivos
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['name'][0])) {
            foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES['documents']['name'][$key]);
                    $timestamp = time();
                    $new_file_name = $timestamp . '_' . $file_name;
                    $file_path = $target_dir . $new_file_name;

                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $rel_path = "$work_site/$folder_name/$new_file_name";

                        $insertDoc = $pdo->prepare("INSERT INTO member_documents (member_id, file_path) VALUES (:member_id, :file_path)");
                        $insertDoc->execute([
                            ':member_id' => $id,
                            ':file_path' => $rel_path
                        ]);
                    }
                }
            }
        }

        // Eliminar documentos marcados para borrar (si viene delete_docs[])
        if (isset($_POST['delete_docs']) && is_array($_POST['delete_docs'])) {
            foreach ($_POST['delete_docs'] as $doc_id) {
                // Primero obtener path para borrar archivo físico
                $stmtDel = $pdo->prepare("SELECT file_path FROM member_documents WHERE id = :doc_id");
                $stmtDel->execute([':doc_id' => $doc_id]);
                $doc = $stmtDel->fetch();

                if ($doc) {
                    $full_path = "../uploads/" . $doc['file_path'];
                    if (file_exists($full_path)) {
                        unlink($full_path);
                    }
                    // Borrar registro de DB
                    $stmtDelDB = $pdo->prepare("DELETE FROM member_documents WHERE id = :doc_id");
                    $stmtDelDB->execute([':doc_id' => $doc_id]);
                }
            }
        }

        $_SESSION['success'] = "Socio actualizado correctamente.";
        header('location: ../members.php');
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error al actualizar socio: " . $e->getMessage();
        header('location: ../members.php');
        exit();
    }
} else {
    header('location: ../members.php');
    exit();
}
