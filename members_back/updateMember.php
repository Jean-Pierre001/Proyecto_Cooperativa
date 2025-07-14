<?php
include '../includes/session.php';
require_once '../includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE members SET member_number = :member_number, name = :name, cuil = :cuil, phone = :phone, email = :email, address = :address, entry_date = :entry_date, exit_date = :exit_date, status = :status, work_site = :work_site WHERE id = :id");

        $stmt->execute([
            ':id' => $_POST['id'],
            ':member_number' => $_POST['member_number'],
            ':name' => $_POST['name'],
            ':cuil' => $_POST['cuil'],
            ':phone' => $_POST['phone'],
            ':email' => $_POST['email'],
            ':address' => $_POST['address'],
            ':entry_date' => $_POST['entry_date'] ?: null,
            ':exit_date' => $_POST['exit_date'] ?: null,
            ':status' => $_POST['status'],
            ':work_site' => $_POST['work_site']
        ]);

        // Eliminar documentos seleccionados
        if (!empty($_POST['delete_docs'])) {
            $deleteStmt = $pdo->prepare("DELETE FROM member_documents WHERE id = :id");
            foreach ($_POST['delete_docs'] as $docId) {
                $deleteStmt->execute([':id' => $docId]);
            }
        }

        // Carpeta según obra
        $target_dir = '../uploads/' . $_POST['work_site'] . '/';
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        // Subir nuevos documentos
        if (isset($_FILES['documents']) && !empty($_FILES['documents']['tmp_name'][0])) {
            foreach ($_FILES['documents']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES['documents']['name'][$key]);
                    $file_path = $target_dir . time() . '_' . $file_name;
                    if (move_uploaded_file($tmp_name, $file_path)) {
                        $rel_path = $_POST['work_site'] . '/' . basename($file_path);
                        $insertStmt = $pdo->prepare("INSERT INTO member_documents (member_id, file_path) VALUES (:member_id, :file_path)");
                        $insertStmt->execute([
                            ':member_id' => $_POST['id'],
                            ':file_path' => $rel_path
                        ]);
                    }
                }
            }
        }

        $_SESSION['success'] = "Socio actualizado correctamente.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error al actualizar socio: " . $e->getMessage();
    }
}

header('Location: ../members.php');
exit();
?>
