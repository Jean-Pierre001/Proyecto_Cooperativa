<?php
require_once '../includes/conn.php';
session_start();

try {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        $_SESSION['error'] = 'ID de miembro no proporcionado.';
        header('Location: ../members.php');
        exit();
    }

    // Datos actualizados del formulario
    $name = $_POST['name'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $phone = $_POST['phone'] ?? null;
    $email = $_POST['email'] ?? null;
    $address = $_POST['address'] ?? null;
    $entry_date = $_POST['entry_date'] ?? null;
    $status = $_POST['status'] ?? 'active';
    $contributions = $_POST['contributions'] ?? 0.00;

    // Validación básica
    if (empty($name) || empty($dni)) {
        $_SESSION['error'] = 'Nombre y DNI son obligatorios.';
        header('Location: ../members.php');
        exit();
    }

    // Actualizar datos del socio
    $stmt = $pdo->prepare("UPDATE members SET name = :name, dni = :dni, phone = :phone, email = :email, address = :address, 
                            entry_date = :entry_date, status = :status, contributions = :contributions WHERE id = :id");
    $stmt->execute([
        ':name' => $name,
        ':dni' => $dni,
        ':phone' => $phone,
        ':email' => $email,
        ':address' => $address,
        ':entry_date' => $entry_date,
        ':status' => $status,
        ':contributions' => $contributions,
        ':id' => $id
    ]);

    // -------------------- ELIMINAR DOCUMENTOS -------------------------
    if (!empty($_POST['delete_docs']) && is_array($_POST['delete_docs'])) {
        $delete_ids = $_POST['delete_docs'];
        $placeholders = implode(',', array_fill(0, count($delete_ids), '?'));

        // Obtener rutas de archivos a eliminar
        $stmtFiles = $pdo->prepare("SELECT file_path FROM member_documents WHERE id IN ($placeholders)");
        $stmtFiles->execute($delete_ids);
        $files = $stmtFiles->fetchAll(PDO::FETCH_COLUMN);

        foreach ($files as $file) {
            $filePath = __DIR__ . '/../uploads/' . $file;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Borrar registros de documentos en BD
        $stmtDelete = $pdo->prepare("DELETE FROM member_documents WHERE id IN ($placeholders)");
        $stmtDelete->execute($delete_ids);
    }

    // -------------------- SUBIR NUEVOS DOCUMENTOS -------------------------
    if (!empty($_FILES['documents']['name'][0])) {
        $uploadBaseDir = __DIR__ . '/../uploads/';  // Carpeta base uploads/
        $memberUploadDir = $uploadBaseDir . $id . '/';  // Carpeta específica del socio

        if (!is_dir($memberUploadDir)) {
            mkdir($memberUploadDir, 0755, true);
        }

        foreach ($_FILES['documents']['name'] as $index => $filename) {
            $tmpName = $_FILES['documents']['tmp_name'][$index];
            $fileSize = $_FILES['documents']['size'][$index];

            if ($fileSize > 0 && is_uploaded_file($tmpName)) {
                $safeName = time() . '_' . basename($filename);
                $destination = $memberUploadDir . $safeName;

                if (move_uploaded_file($tmpName, $destination)) {
                    // Guardamos la ruta relativa con carpeta del socio para luego poder acceder fácilmente
                    $relativePath = $id . '/' . $safeName;

                    $stmt = $pdo->prepare("INSERT INTO member_documents (member_id, file_path) VALUES (:member_id, :file_path)");
                    $stmt->execute([
                        ':member_id' => $id,
                        ':file_path' => $relativePath
                    ]);
                }
            }
        }
    }

    $_SESSION['success'] = 'Socio actualizado correctamente.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
}

header('Location: ../members.php');
exit();
