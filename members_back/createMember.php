<?php
require_once '../includes/conn.php';
session_start();

try {
    // Obtener y sanitizar datos del formulario
    $name = $_POST['name'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $phone = $_POST['phone'] ?? null;
    $email = $_POST['email'] ?? null;
    $address = $_POST['address'] ?? null;
    $entry_date = $_POST['entry_date'] ?? null;
    $status = $_POST['status'] ?? 'active';
    $contributions = $_POST['contributions'] ?? 0.00;

    // Validaciones mínimas
    if (empty($name) || empty($dni)) {
        $_SESSION['error'] = 'Nombre y DNI son obligatorios.';
        header('Location: ../members.php');
        exit();
    }

    // Insertar miembro
    $stmt = $pdo->prepare("INSERT INTO members (name, dni, phone, email, address, entry_date, status, contributions) 
                           VALUES (:name, :dni, :phone, :email, :address, :entry_date, :status, :contributions)");
    $stmt->execute([
        ':name' => $name,
        ':dni' => $dni,
        ':phone' => $phone,
        ':email' => $email,
        ':address' => $address,
        ':entry_date' => $entry_date,
        ':status' => $status,
        ':contributions' => $contributions
    ]);

    $member_id = $pdo->lastInsertId();

    // Ruta base de uploads
    $uploadBaseDir = __DIR__ . '/../uploads/';

    // Crear carpeta específica para el socio, usando el ID
    $memberUploadDir = $uploadBaseDir . $member_id . '/';

    if (!is_dir($memberUploadDir)) {
        mkdir($memberUploadDir, 0755, true);
    }

    // Manejar documentos subidos
    if (!empty($_FILES['documents']['name'][0])) {
        foreach ($_FILES['documents']['name'] as $index => $filename) {
            $tmpName = $_FILES['documents']['tmp_name'][$index];
            $fileSize = $_FILES['documents']['size'][$index];

            if ($fileSize > 0 && is_uploaded_file($tmpName)) {
                // Para evitar colisiones de nombres, agregamos timestamp
                $safeName = time() . '_' . basename($filename);
                $destination = $memberUploadDir . $safeName;

                if (move_uploaded_file($tmpName, $destination)) {
                    // Guardar solo el nombre relativo (con carpeta id/)
                    $relativePath = $member_id . '/' . $safeName;

                    // Insertar en member_documents con la ruta relativa
                    $stmt = $pdo->prepare("INSERT INTO member_documents (member_id, file_path) VALUES (:member_id, :file_path)");
                    $stmt->execute([
                        ':member_id' => $member_id,
                        ':file_path' => $relativePath
                    ]);
                }
            }
        }
    }

    $_SESSION['success'] = 'Socio creado correctamente.';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error al crear socio: ' . $e->getMessage();
}

header('Location: ../members.php');
exit();

